<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('Super-Admin');

        // Auto-sync teacher assignments for non-admins
        if (!$isSuperAdmin) {
            CourseOffering::syncTeacherAssignments($user);
        }

        $query = AttendanceSession::with([
            'courseOffering.course',
            'courseOffering.teacher',
            'courseOffering.program',
            'courseOffering.batch',
            'courseOffering.section',
            'courseOffering.academicSession',
            'creator'
        ])->withCount([
            'records as total_students',
            'records as present_students' => function ($q) {
                $q->whereIn('status', ['Present', 'Late']);
            },
            'records as absent_students' => function ($q) {
                $q->where('status', 'Absent');
            },
            'records as leave_students' => function ($q) {
                $q->where('status', 'Leave');
            }
        ]);

        // Restrict non-admin teachers strictly to their assigned course offerings
        if (!$isSuperAdmin) {
            $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
            $query->whereHas('courseOffering', function ($q) use ($user, $employee) {
                $q->where('teacher_id', $user->id);
                if ($employee) {
                    $q->orWhere('teacher_id', $employee->id);
                }
            });
        }

        if ($request->filled('department_id')) {
            $query->whereHas('courseOffering.program', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('program_id')) {
            $query->whereHas('courseOffering', function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }

        if ($request->filled('batch_id')) {
            $query->whereHas('courseOffering', function ($q) use ($request) {
                $q->where('batch_id', $request->batch_id);
            });
        }

        if ($request->filled('teacher_id') && $isSuperAdmin) {
            $query->whereHas('courseOffering', function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            });
        }

        if ($request->filled('course_id')) {
            $query->whereHas('courseOffering', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('academic_session_id')) {
            $query->whereHas('courseOffering', function ($q) use ($request) {
                $q->where('academic_session_id', $request->academic_session_id);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('attendance_date', [$request->start_date, $request->end_date]);
        }

        $perPage = $request->get('per_page', 100);
        $sessions = $query->orderBy('attendance_date', 'desc')->paginate($perPage);

        // Filter dropdown options based on role
        if ($isSuperAdmin) {
            $departments = Department::all();
            $programs = Program::all();
            $batches = Batch::all();
            $courses = Course::all();
            $teachers = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['Teacher', 'Faculty', 'Super Admin']);
            })->get();
        } else {
            $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
            $myOfferingQuery = CourseOffering::where(function ($q) use ($user, $employee) {
                $q->where('teacher_id', $user->id);
                if ($employee) {
                    $q->orWhere('teacher_id', $employee->id);
                }
            });

            $myOfferingIds = $myOfferingQuery->pluck('id');
            $myCourseIds = $myOfferingQuery->pluck('course_id')->unique();
            $myProgramIds = $myOfferingQuery->pluck('program_id')->unique();

            $departments = Department::whereHas('programs', function ($q) use ($myProgramIds) {
                $q->whereIn('id', $myProgramIds);
            })->get();

            $programs = Program::whereIn('id', $myProgramIds)->get();
            $batches = Batch::whereIn('program_id', $myProgramIds)->get();
            $courses = Course::whereIn('id', $myCourseIds)->get();
            $teachers = collect([$user]);
        }

        $academicSessions = AcademicSession::all();

        return view('attendance.reports', compact(
            'sessions',
            'departments',
            'programs',
            'batches',
            'courses',
            'teachers',
            'academicSessions'
        ));
    }
}
