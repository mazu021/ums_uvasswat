<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        $student = Student::where('user_id', $user->id)->first();

        // Fallback for demonstration if user is Super Admin or missing student profile
        if (!$student) {
            $student = Student::with(['program', 'batch', 'department'])->first();
        }

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'No student profile associated with this account.');
        }

        // Get offerings matching student's program, batch, semester, section
        $offerings = $student->activeCourseOfferings()
            ->with(['course', 'teacher', 'academicSession'])
            ->get();

        $subjectStats = [];
        $totalPresentCount = 0;
        $totalSessionCount = 0;

        foreach ($offerings as $offering) {
            // Count attendance sessions for this offering
            $totalLectures = $offering->attendanceSessions()->count();

            // Student's records in this offering
            $studentRecords = AttendanceRecord::with('session')
                ->whereHas('session', function ($q) use ($offering) {
                    $q->where('course_offering_id', $offering->id);
                })
                ->where('student_id', $student->id)
                ->get();

            $presentCount = $studentRecords->whereIn('status', ['Present', 'Late', 'Leave'])->count();
            $absentCount = $studentRecords->where('status', 'Absent')->count();
            
            $percentage = $totalLectures > 0 ? round(($presentCount / $totalLectures) * 100, 1) : 0;

            $totalPresentCount += $presentCount;
            $totalSessionCount += $totalLectures;

            $subjectStats[] = [
                'offering' => $offering,
                'total_lectures' => $totalLectures,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'percentage' => $percentage,
                'records' => $studentRecords,
            ];
        }

        $overallPercentage = $totalSessionCount > 0 ? round(($totalPresentCount / $totalSessionCount) * 100, 1) : 0;

        return view('attendance.student_dashboard', compact(
            'student',
            'subjectStats',
            'overallPercentage',
            'totalSessionCount'
        ));
    }
}
