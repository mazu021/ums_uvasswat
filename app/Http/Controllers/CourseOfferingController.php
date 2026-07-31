<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;

class CourseOfferingController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseOffering::with([
            'course',
            'teacher',
            'program',
            'semester',
            'batch',
            'section',
            'academicSession'
        ]);

        if ($request->filled('academic_session_id')) {
            $query->where('academic_session_id', $request->academic_session_id);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $perPage = $request->get('per_page', 100);
        $offerings = $query->latest()->paginate($perPage);

        $courses = Course::orderBy('course_code')->get();
        $teachers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Teacher', 'Faculty', 'Super Admin']);
        })->orWhere('id', auth()->id())->get();
        
        $programs = Program::where('status', 'active')->get();
        $batches = Batch::orderBy('id', 'desc')->get();
        $semesters = Semester::all();
        $sections = Section::all();
        $academicSessions = AcademicSession::orderBy('id', 'desc')->get();

        return view('course_offerings.index', compact(
            'offerings',
            'courses',
            'teachers',
            'programs',
            'batches',
            'semesters',
            'sections',
            'academicSessions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:programs,id',
            'batch_id' => 'nullable|exists:batches,id',
            'semester_number' => 'required|integer|min:1|max:12',
            'semester_id' => 'nullable|exists:semesters,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        if (empty($validated['batch_id'])) {
            $validated['batch_id'] = Batch::where('program_id', $validated['program_id'])->value('id')
                ?? Batch::firstOrCreate([
                    'code' => 'BATCH-' . $validated['program_id'] . '-F26',
                ], [
                    'program_id' => $validated['program_id'],
                    'academic_session_id' => $validated['academic_session_id'],
                    'name' => 'Default Academic Batch',
                    'status' => 'active',
                ])->id;
        }

        // Check for duplicate offering
        $exists = CourseOffering::where('course_id', $validated['course_id'])
            ->where('program_id', $validated['program_id'])
            ->where('batch_id', $validated['batch_id'])
            ->where('section_id', $validated['section_id'] ?? null)
            ->where('academic_session_id', $validated['academic_session_id'])
            ->where('teacher_id', $validated['teacher_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'An identical course offering already exists for this teacher, batch, and session.');
        }

        CourseOffering::create($validated);

        return redirect()->route('course-offerings.index')->with('success', 'Course Offering created successfully.');
    }

    public function update(Request $request, CourseOffering $courseOffering)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'semester_number' => 'required|integer|min:1|max:12',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $courseOffering->update($validated);

        return redirect()->route('course-offerings.index')->with('success', 'Course Offering updated successfully.');
    }

    public function destroy(CourseOffering $courseOffering)
    {
        $teacherId = $courseOffering->teacher_id;
        $courseId = $courseOffering->course_id;

        // Delete associated legacy course assignments if present
        $employee = \App\Models\Employee::where('user_id', $teacherId)->first();
        if ($employee) {
            \App\Models\CourseAssignment::where('course_id', $courseId)
                ->where('employee_id', $employee->id)
                ->delete();
        }

        $courseOffering->delete();

        return redirect()->route('course-offerings.index')->with('success', 'Course Offering deleted and instructor unassigned.');
    }

    public function exportStudents(CourseOffering $courseOffering)
    {
        $courseOffering->load(['course', 'program', 'batch', 'section', 'academicSession']);
        $students = $courseOffering->getEnrolledStudents();

        $filename = "Student_List_" . ($courseOffering->course->course_code ?? 'Course') . "_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($students, $courseOffering) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header info
            fputcsv($file, ['UNIVERSITY OF VETERINARY AND ANIMAL SCIENCES, SWAT (UVAS SWAT)']);
            fputcsv($file, ['STUDENT ROSTER FOR ASSIGNED COURSE OFFERING']);
            fputcsv($file, ['Course Code:', $courseOffering->course->course_code ?? '', 'Course Title:', $courseOffering->course->title ?? '']);
            fputcsv($file, ['Program:', $courseOffering->program->name ?? '', 'Batch:', $courseOffering->batch->name ?? '']);
            fputcsv($file, ['Semester:', $courseOffering->semester_number, 'Section:', $courseOffering->section->name ?? 'N/A']);
            fputcsv($file, ['Teacher:', $courseOffering->teacher->name ?? '', 'Academic Session:', $courseOffering->academicSession->name ?? '']);
            fputcsv($file, []);

            // Data Columns
            fputcsv($file, ['#', 'Roll Number', 'Registration Number', 'Student Name', 'Father Name', 'Gender', 'Email', 'Phone', 'Status']);

            foreach ($students as $index => $student) {
                fputcsv($file, [
                    $index + 1,
                    $student->roll_number,
                    $student->registration_number,
                    $student->full_name,
                    $student->father_name ?? '',
                    ucfirst($student->gender),
                    $student->email,
                    $student->phone ?? '',
                    ucfirst($student->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
