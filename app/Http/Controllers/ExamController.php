<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseOfferingGrade;
use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\Student;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Auto-sync any teacher assignments given in course module
        CourseOffering::syncTeacherAssignments($user);

        $selectedOfferingId = $request->get('course_offering_id');

        // Resolve offerings accessible to current user
        $offeringsQuery = CourseOffering::with(['course', 'program', 'batch', 'section', 'teacher', 'academicSession'])
            ->where('status', 'active');

        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin') && !$user->hasRole('HOD')) {
            $employee = \App\Models\Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
            $offeringsQuery->where(function ($q) use ($user, $employee) {
                $q->where('teacher_id', $user->id);
                if ($employee) {
                    $q->orWhere('teacher_id', $employee->id);
                }
            });
        }

        $offerings = $offeringsQuery->get();

        $selectedOffering = null;
        $students = collect();
        $gradesMap = [];

        if ($selectedOfferingId) {
            $selectedOffering = $offerings->firstWhere('id', $selectedOfferingId) 
                ?? CourseOffering::with(['course', 'program', 'batch', 'section', 'teacher', 'academicSession'])->find($selectedOfferingId);
        } elseif ($offerings->count() > 0) {
            $selectedOffering = $offerings->first();
        }

        if ($selectedOffering) {
            $students = $selectedOffering->getEnrolledStudents();
            $existingGrades = CourseOfferingGrade::where('course_offering_id', $selectedOffering->id)->get();
            foreach ($existingGrades as $g) {
                $gradesMap[$g->student_id] = $g;
            }
        }

        return view('academics.exams', compact('offerings', 'selectedOffering', 'students', 'gradesMap'));
    }

    public function saveGradebook(Request $request)
    {
        $validated = $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'grades' => 'required|array',
            'grades.*.mid_marks' => 'nullable|numeric|min:0|max:30',
            'grades.*.internal_marks' => 'nullable|numeric|min:0|max:20',
            'grades.*.final_marks' => 'nullable|numeric|min:0|max:50',
            'grades.*.remarks' => 'nullable|string|max:255',
        ]);

        $offering = CourseOffering::findOrFail($validated['course_offering_id']);

        foreach ($validated['grades'] as $studentId => $data) {
            $mid = (isset($data['mid_marks']) && $data['mid_marks'] !== '') ? (float) $data['mid_marks'] : null;
            $internal = (isset($data['internal_marks']) && $data['internal_marks'] !== '') ? (float) $data['internal_marks'] : null;
            $final = (isset($data['final_marks']) && $data['final_marks'] !== '') ? (float) $data['final_marks'] : null;
            $remarks = $data['remarks'] ?? null;

            $calculated = CourseOfferingGrade::calculateGradeAndGpa($mid, $internal, $final);

            CourseOfferingGrade::updateOrCreate(
                [
                    'course_offering_id' => $offering->id,
                    'student_id' => $studentId,
                ],
                [
                    'mid_marks' => $mid,
                    'internal_marks' => $internal,
                    'final_marks' => $final,
                    'total_marks' => $calculated['total_marks'],
                    'grade' => $calculated['grade'],
                    'gpa_point' => $calculated['gpa_point'],
                    'remarks' => $remarks,
                ]
            );
        }

        AuditService::log('Updated Course Gradebook', 'CourseOffering', $offering->id, ['code' => $offering->course->code ?? 'N/A']);

        return back()->with('success', 'Class Gradebook marks submitted and CGPA updated successfully.');
    }

    public function exportGradebook(CourseOffering $offering): StreamedResponse
    {
        $offering->load(['course', 'program', 'teacher', 'academicSession']);
        $students = $offering->getEnrolledStudents();
        $grades = CourseOfferingGrade::where('course_offering_id', $offering->id)->get()->keyBy('student_id');

        $courseCode = $offering->course->code ?? 'COURSE';
        $programCode = $offering->program->code ?? 'PROG';
        $fileName = "Gradebook_{$courseCode}_{$programCode}_Sem" . ($offering->semester_number ?? 1) . ".csv";

        $response = new StreamedResponse(function () use ($students, $grades, $offering) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header Title Rows
            fputcsv($handle, ['THE UNIVERSITY OF VETERINARY AND ANIMAL SCIENCES, SWAT']);
            fputcsv($handle, ['COURSE GRADEBOOK & RESULT SHEET']);
            fputcsv($handle, ['Course:', ($offering->course->code ?? '') . ' - ' . ($offering->course->title ?? '')]);
            fputcsv($handle, ['Program:', $offering->program->name ?? 'N/A']);
            fputcsv($handle, ['Instructor:', $offering->teacher->full_name ?? 'N/A']);
            fputcsv($handle, ['Session:', $offering->academicSession->name ?? 'Fall 2026']);
            fputcsv($handle, ['Weightage:', 'Mid Exam (30%) | Internal (20%) | Final Exam (50%)']);
            fputcsv($handle, []);

            // Data Headers
            fputcsv($handle, [
                'S.No',
                'Registration Number',
                'Roll Number',
                'Student Name',
                'Mid Exam (30)',
                'Internal Marks (20)',
                'Final Exam (50)',
                'Total Marks (100)',
                'Letter Grade',
                'GPA Point (4.00)',
            ]);

            $index = 1;
            foreach ($students as $student) {
                $g = $grades->get($student->id);
                fputcsv($handle, [
                    $index++,
                    $student->registration_number ?? 'N/A',
                    $student->roll_number ?? 'N/A',
                    $student->full_name,
                    $g && $g->mid_marks !== null ? number_format($g->mid_marks, 2) : '-',
                    $g && $g->internal_marks !== null ? number_format($g->internal_marks, 2) : '-',
                    $g && $g->final_marks !== null ? number_format($g->final_marks, 2) : '-',
                    $g && $g->total_marks !== null ? number_format($g->total_marks, 2) : '-',
                    $g->grade ?? 'N/A',
                    $g && $g->gpa_point !== null ? number_format($g->gpa_point, 2) : '0.00',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$fileName}\"");

        return $response;
    }
}
