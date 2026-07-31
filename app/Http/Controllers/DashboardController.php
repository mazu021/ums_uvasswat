<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Employee;
use App\Models\FeeChallan;
use App\Models\LeaveApplication;
use App\Models\LedgerEntry;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Student')) {
            return redirect()->route('student.dashboard');
        }

        if ($user->hasRole('Faculty') || $user->hasRole('Teacher')) {
            // Auto sync any assignments for this teacher
            \App\Models\CourseOffering::syncTeacherAssignments($user);

            $myOfferings = \App\Models\CourseOffering::with(['course', 'program', 'batch', 'section', 'academicSession'])
                ->where('teacher_id', $user->id)
                ->where('status', 'active')
                ->get();

            $employee = Employee::where('user_id', $user->id)->first();
            $myLeaves = $employee ? LeaveApplication::where('employee_id', $employee->id)->latest()->take(5)->get() : collect();

            return view('dashboard.faculty', compact('user', 'myOfferings', 'myLeaves'));
        }

        $roles = $user->getRoleNames();
        $primaryRole = $roles->first() ?? 'Student';

        // General Stats
        $totalStudents = Student::where('status', 'active')->count();
        $totalEmployees = Employee::where('status', 'active')->count();
        $totalFaculty = Employee::where('status', 'active')->where('type', 'faculty')->count();
        $totalStaff = Employee::where('status', 'active')->where('type', 'staff')->count();

        // Today's Attendance Stats
        $today = Carbon::today();
        $todayAttendance = Attendance::whereDate('date', $today)->count();
        $presentToday = Attendance::whereDate('date', $today)->whereIn('status', ['present', 'Present'])->count();
        $absentToday = Attendance::whereDate('date', $today)->whereIn('status', ['absent', 'Absent'])->count();
        $lateToday = Attendance::whereDate('date', $today)->whereIn('status', ['late', 'Late'])->count();

        // Financials Summary
        $totalIncome = LedgerEntry::where('entry_type', 'credit')->sum('amount');
        $totalExpense = LedgerEntry::where('entry_type', 'debit')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $unpaidFeeAmount = FeeChallan::whereIn('status', ['unpaid', 'overdue'])->sum('total_amount');
        $paidFeeAmount = FeeChallan::where('status', 'paid')->sum('paid_amount');

        // Recent Announcements
        $announcements = Announcement::where('is_published', true)
            ->latest()
            ->take(5)
            ->get();

        // Pending Leaves
        $pendingLeaves = LeaveApplication::with(['employee', 'leaveType'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Role Specific Context
        $studentData = null;
        $facultyData = null;

        if ($user->hasRole('Student')) {
            $studentData = Student::with(['department', 'feeChallans', 'examGrades.exam.course'])->where('user_id', $user->id)->first();
        }

        if ($user->hasRole('Faculty')) {
            $employee = Employee::with(['department', 'courseAssignments.course'])->where('user_id', $user->id)->first();
            $facultyData = $employee;
        }

        // Chart Data (Monthly Income vs Expense)
        $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        $incomeData = [1200000, 1500000, 1800000, 2100000, 3200000, 4100000, 4850000];
        $expenseData = [900000, 1100000, 1300000, 1600000, 2200000, 2600000, 2830000];

        return view('dashboard.index', compact(
            'user',
            'primaryRole',
            'totalStudents',
            'totalEmployees',
            'totalFaculty',
            'totalStaff',
            'todayAttendance',
            'presentToday',
            'absentToday',
            'lateToday',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'unpaidFeeAmount',
            'paidFeeAmount',
            'announcements',
            'pendingLeaves',
            'studentData',
            'facultyData',
            'chartMonths',
            'incomeData',
            'expenseData'
        ));
    }
}
