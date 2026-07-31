<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\FeeChallan;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPortalController extends Controller
{
    private function getStudent()
    {
        $user = Auth::user();
        $student = Student::with(['department.faculty', 'feeChallans', 'offeringGrades.courseOffering.course'])
            ->where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        if ($student && !$student->user_id) {
            $student->update(['user_id' => $user->id]);
        }

        return $student;
    }

    public function dashboard()
    {
        $student = $this->getStudent();
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not linked to account.');
        }

        // Active Course Offerings for student's program/department & current semester
        $activeOfferings = $student->activeCourseOfferings()
            ->with(['course.department', 'teacher', 'program', 'academicSession'])
            ->get();

        // Convert offerings to course list with assigned teacher info for view compatibility
        $courses = $activeOfferings->map(function ($offering) {
            $course = $offering->course;
            if ($course) {
                $course->assigned_teacher_name = $offering->teacher ? $offering->teacher->name : 'Faculty Assigned';
                $course->assigned_teacher_email = $offering->teacher ? $offering->teacher->email : '';
                $course->offering_program_name = $offering->program ? $offering->program->name : '';
            }
            return $course;
        })->filter();

        // Course Offering Grades & CGPA calculation
        $offeringGrades = \App\Models\CourseOfferingGrade::with('courseOffering.course')
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('course_offering_id');

        $cgpa = $student->calculateCgpa();

        // Upcoming Exams
        $upcomingExams = Exam::with('course')
            ->whereIn('course_id', $courses->pluck('id'))
            ->where('exam_date', '>=', Carbon::today())
            ->orderBy('exam_date')
            ->get();

        // Attendance stats from Course Offerings & AttendanceRecord
        $totalLectures = 0;
        $presentLectures = 0;

        foreach ($activeOfferings as $offering) {
            $offeringLectures = $offering->attendanceSessions()->count();
            $totalLectures += $offeringLectures;

            $records = \App\Models\AttendanceRecord::whereHas('session', function ($q) use ($offering) {
                $q->where('course_offering_id', $offering->id);
            })->where('student_id', $student->id)->get();

            $presentLectures += $records->whereIn('status', ['Present', 'Late', 'Leave'])->count();
        }

        $attendancePercentage = $totalLectures > 0 
            ? number_format(($presentLectures / $totalLectures) * 100, 1) 
            : '0.0';

        // Student Announcements
        $announcements = Announcement::where('is_published', true)
            ->whereIn('target_role', ['all', 'student'])
            ->latest()
            ->take(4)
            ->get();

        return view('student_portal.dashboard', compact(
            'student',
            'courses',
            'activeOfferings',
            'offeringGrades',
            'cgpa',
            'upcomingExams',
            'attendancePercentage',
            'announcements'
        ));
    }

    public function courses()
    {
        $student = $this->getStudent();
        if (!$student) return redirect()->route('dashboard');

        // 1. Current active semester offerings
        $activeOfferings = $student->activeCourseOfferings()
            ->with(['course.department', 'teacher', 'program', 'academicSession'])
            ->get();

        // 2. All historical grades & course offerings for this student across all semesters
        $allOfferingGrades = \App\Models\CourseOfferingGrade::with([
                'courseOffering.course.department',
                'courseOffering.teacher',
                'courseOffering.program',
                'courseOffering.academicSession'
            ])
            ->where('student_id', $student->id)
            ->get();

        // Group grades by semester_number
        $gradesBySemester = $allOfferingGrades->groupBy(function ($g) {
            return $g->courseOffering->semester_number ?? 1;
        });

        // 3. Build complete semester-by-semester repository map (Semester 1 to Program Total Semesters)
        $totalProgramSemesters = $student->program->total_semesters ?? 8;
        $semesterRepository = [];
        $totalCompletedCredits = 0;
        $totalPassedCourses = 0;

        for ($sem = 1; $sem <= $totalProgramSemesters; $sem++) {
            $semGrades = $gradesBySemester->get($sem, collect());

            // Calculate semester stats
            $semCredits = 0;
            $semQualityPoints = 0;

            foreach ($semGrades as $g) {
                $ch = $g->courseOffering->course->credit_hours ?? 3;
                $semCredits += $ch;
                $semQualityPoints += ($g->gpa_point * $ch);
                if ($g->grade && !in_array(strtoupper(trim($g->grade)), ['F', 'W', 'I'])) {
                    $totalPassedCourses++;
                    $totalCompletedCredits += $ch;
                }
            }

            $semGpa = $semCredits > 0 ? number_format($semQualityPoints / $semCredits, 2) : '0.00';

            $semesterRepository[$sem] = [
                'semester' => $sem,
                'is_current' => ($sem == $student->current_semester),
                'is_past' => ($sem < $student->current_semester),
                'is_future' => ($sem > $student->current_semester),
                'grades' => $semGrades,
                'total_credits' => $semCredits,
                'gpa' => $semGpa,
            ];
        }

        $cgpa = $student->calculateCgpa();

        return view('student_portal.courses', compact(
            'student',
            'activeOfferings',
            'allOfferingGrades',
            'semesterRepository',
            'totalProgramSemesters',
            'totalCompletedCredits',
            'totalPassedCourses',
            'cgpa'
        ));
    }

    public function attendance()
    {
        return redirect()->route('attendance.student.dashboard');
    }

    public function exams()
    {
        $student = $this->getStudent();
        if (!$student) return redirect()->route('dashboard');

        $activeOfferings = $student->activeCourseOfferings()
            ->with(['course', 'program', 'teacher', 'academicSession'])
            ->get();

        $offeringGrades = \App\Models\CourseOfferingGrade::with(['courseOffering.course', 'courseOffering.program'])
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('course_offering_id');

        $cgpa = $student->calculateCgpa();

        return view('student_portal.exams', compact('student', 'activeOfferings', 'offeringGrades', 'cgpa'));
    }

    public function exportStudentResult()
    {
        $student = $this->getStudent();
        if (!$student) return redirect()->route('dashboard');

        $activeOfferings = $student->activeCourseOfferings()
            ->with(['course', 'program', 'teacher'])
            ->get();

        $offeringGrades = \App\Models\CourseOfferingGrade::with('courseOffering.course')
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('course_offering_id');

        $cgpa = $student->calculateCgpa();
        $fileName = "Result_Sheet_" . str_replace(['/', ' '], '_', $student->registration_number ?? 'Student') . ".csv";

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($student, $activeOfferings, $offeringGrades, $cgpa) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['THE UNIVERSITY OF VETERINARY AND ANIMAL SCIENCES, SWAT']);
            fputcsv($handle, ['STUDENT EXAM RESULT SHEET & ACADEMIC TRANSCRIPT']);
            fputcsv($handle, ['Student Name:', $student->full_name]);
            fputcsv($handle, ['Registration No:', $student->registration_number ?? 'N/A']);
            fputcsv($handle, ['Roll Number:', $student->roll_number ?? 'N/A']);
            fputcsv($handle, ['Degree Program:', $student->program->name ?? ($student->department->name ?? 'N/A')]);
            fputcsv($handle, ['Current Semester:', 'Semester ' . ($student->current_semester ?? 1)]);
            fputcsv($handle, ['Cumulative GPA (CGPA):', $cgpa . ' / 4.00']);
            fputcsv($handle, []);

            fputcsv($handle, [
                'S.No',
                'Course Code',
                'Course Title',
                'Credit Hours',
                'Mid Exam (30%)',
                'Internal Marks (20%)',
                'Final Exam (50%)',
                'Total Marks (100%)',
                'Letter Grade',
                'GPA Point',
            ]);

            $idx = 1;
            foreach ($activeOfferings as $offering) {
                $g = $offeringGrades->get($offering->id);
                fputcsv($handle, [
                    $idx++,
                    $offering->course->code ?? 'N/A',
                    $offering->course->title ?? 'N/A',
                    $offering->course->credit_hours ?? 3,
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

    public function feeChallans()
    {
        $student = $this->getStudent();
        if (!$student) return redirect()->route('dashboard');

        $currSem = $student->current_semester ?: 1;

        // Check if student has a fee challan issued for their current semester
        $currChallan = FeeChallan::where('student_id', $student->id)
            ->where('semester', $currSem)
            ->first();

        if (!$currChallan) {
            // Find fee structure for student's program/department for their current semester
            $fs = FeeStructure::where('program_id', $student->program_id)->where('semester', $currSem)->first()
                ?? FeeStructure::where('department_id', $student->department_id)->where('semester', $currSem)->first()
                ?? FeeStructure::where('program_id', $student->program_id)->first()
                ?? FeeStructure::first();

            if ($fs) {
                $challanNo = 'CH-' . date('Y') . '-S' . $currSem . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT);
                $dueDate = \Carbon\Carbon::today()->addDays(30);

                FeeChallan::create([
                    'student_id' => $student->id,
                    'fee_structure_id' => $fs->id,
                    'semester' => $currSem,
                    'challan_number' => $challanNo,
                    'issue_date' => \Carbon\Carbon::today(),
                    'due_date' => $dueDate,
                    'total_amount' => $fs->total_amount,
                    'late_fine_amount' => 0.00,
                    'status' => 'unpaid',
                ]);
            }
        }

        $challans = FeeChallan::with('feeStructure')
            ->where('student_id', $student->id)
            ->orderBy('semester', 'desc')
            ->latest()
            ->get();

        return view('student_portal.fees', compact('student', 'challans'));
    }

    public function uploadFeeProof(Request $request, FeeChallan $feeChallan)
    {
        $student = $this->getStudent();
        if ($feeChallan->student_id !== $student->id) {
            return back()->with('error', 'Unauthorized fee challan action.');
        }

        $validated = $request->validate([
            'transaction_reference' => 'required|string|max:255',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'payment_notes' => 'nullable|string',
        ]);

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('fee_proofs', 'public');
            $validated['payment_proof'] = $path;
        }

        $feeChallan->update([
            'payment_proof' => $validated['payment_proof'] ?? null,
            'transaction_reference' => $validated['transaction_reference'],
            'payment_notes' => $validated['payment_notes'] ?? null,
            'rejection_reason' => null,
            'status' => 'pending_verification',
        ]);

        AuditService::log('Uploaded Fee Payment Proof', 'FeeChallan', $feeChallan->id, ['challan' => $feeChallan->challan_number]);

        return back()->with('success', "Payment proof for Challan {$feeChallan->challan_number} uploaded successfully. Awaiting HOD / Finance verification.");
    }

    public function verifyFeeProof(Request $request, FeeChallan $feeChallan)
    {
        $validated = $request->validate([
            'status' => 'required|in:paid,unpaid',
            'notes' => 'nullable|string',
        ]);

        $feeChallan->update([
            'status' => $validated['status'],
            'paid_amount' => $validated['status'] === 'paid' ? $feeChallan->total_amount : 0.00,
            'paid_at' => $validated['status'] === 'paid' ? now() : null,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        AuditService::log("Verified Student Fee Proof ({$validated['status']})", 'FeeChallan', $feeChallan->id);

        return back()->with('success', "Challan {$feeChallan->challan_number} payment status updated to " . ucfirst($validated['status']) . ".");
    }

    public function transcript()
    {
        $student = $this->getStudent();
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not linked to account.');
        }

        $student->load(['program.department', 'batch']);

        $grades = \App\Models\CourseOfferingGrade::with(['courseOffering.course', 'courseOffering.teacher', 'courseOffering.program'])
            ->where('student_id', $student->id)
            ->get();

        $cgpa = $student->calculateCgpa();

        $totalCreditHours = 0;
        $totalQualityPoints = 0;

        foreach ($grades as $g) {
            $ch = $g->courseOffering->course->credit_hours ?? 3;
            $totalCreditHours += $ch;
            $totalQualityPoints += ($g->gpa_point * $ch);
        }

        return view('student_portal.transcript', compact(
            'student',
            'grades',
            'cgpa',
            'totalCreditHours',
            'totalQualityPoints'
        ));
    }
}
