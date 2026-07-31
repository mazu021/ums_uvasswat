<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\CourseOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceEngineController extends Controller
{
    /**
     * Teacher Dashboard showing assigned course offerings
     */
    public function teacherDashboard()
    {
        $user = auth()->user();

        // Auto-sync any assignments for this teacher
        CourseOffering::syncTeacherAssignments($user);

        // If user is Admin, they can view all active offerings or filter
        $query = CourseOffering::with([
            'course',
            'program',
            'batch',
            'section',
            'academicSession',
            'attendanceSessions' => function ($q) {
                $q->latest()->limit(5);
            }
        ])->where('status', 'active');

        // Non-admin teachers only see their assigned offerings
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin')) {
            $query->where('teacher_id', $user->id);
        }

        $offerings = $query->get();

        return view('attendance.teacher_dashboard', compact('offerings'));
    }

    /**
     * Display sheet for marking attendance for a specific course offering
     */
    public function markAttendanceForm(CourseOffering $courseOffering)
    {
        // Security check
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin') && $courseOffering->teacher_id !== $user->id) {
            abort(403, 'Unauthorized. You are not assigned to this course offering.');
        }

        $courseOffering->load(['course', 'program', 'batch', 'section', 'academicSession']);
        
        // Auto-populate students belonging to this program, batch, semester, section
        $students = $courseOffering->getEnrolledStudents();

        // Calculate current lecture number for today or this offering
        $lastLecture = AttendanceSession::where('course_offering_id', $courseOffering->id)->max('lecture_number') ?? 0;
        $nextLecture = $lastLecture + 1;

        return view('attendance.mark', compact('courseOffering', 'students', 'nextLecture'));
    }

    /**
     * Save attendance session and student attendance records
     */
    public function storeAttendance(Request $request, CourseOffering $courseOffering)
    {
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin') && $courseOffering->teacher_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'lecture_number' => 'required|integer|min:1|max:20',
            'topic' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:Present,Absent,Leave,Late',
            'attendance.*.remarks' => 'nullable|string|max:255',
        ]);

        // Duplicate attendance check
        $existingSession = AttendanceSession::where('course_offering_id', $courseOffering->id)
            ->where('attendance_date', $validated['attendance_date'])
            ->where('lecture_number', $validated['lecture_number'])
            ->first();

        if ($existingSession) {
            return back()->withInput()->with('error', "Attendance for Lecture #{$validated['lecture_number']} on {$validated['attendance_date']} has already been marked.");
        }

        DB::transaction(function () use ($courseOffering, $validated, $user) {
            $session = AttendanceSession::create([
                'course_offering_id' => $courseOffering->id,
                'attendance_date' => $validated['attendance_date'],
                'lecture_number' => $validated['lecture_number'],
                'topic' => $validated['topic'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => $user->id,
            ]);

            foreach ($validated['attendance'] as $record) {
                AttendanceRecord::create([
                    'attendance_session_id' => $session->id,
                    'student_id' => $record['student_id'],
                    'status' => $record['status'],
                    'remarks' => $record['remarks'] ?? null,
                ]);
            }
        });

        return redirect()->route('attendance.teacher.dashboard')->with('success', 'Attendance marked successfully!');
    }

    /**
     * View history of attendance sessions for a course offering
     */
    public function offeringHistory(Request $request, CourseOffering $courseOffering)
    {
        $courseOffering->load(['course', 'program', 'batch', 'section', 'academicSession']);
        $perPage = $request->get('per_page', 100);
        
        $sessions = AttendanceSession::withCount(['records as present_count' => function ($q) {
            $q->whereIn('status', ['Present', 'Late']);
        }, 'records as total_students'])
        ->where('course_offering_id', $courseOffering->id)
        ->orderBy('attendance_date', 'desc')
        ->orderBy('lecture_number', 'desc')
        ->paginate($perPage);

        return view('attendance.history', compact('courseOffering', 'sessions'));
    }

    /**
     * View single session attendance sheet detail
     */
    public function showSession(AttendanceSession $attendanceSession)
    {
        $attendanceSession->load([
            'courseOffering.course',
            'courseOffering.program',
            'courseOffering.batch',
            'records.student'
        ]);

        return view('attendance.session_detail', compact('attendanceSession'));
    }
}
