<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveApplicationRequest;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $leaveTypes = LeaveType::all();
        $user = Auth::user();
        $perPage = $request->get('per_page', 50);

        // Check if user is Admin / HR / Management
        $isAdmin = $user->hasRole('Super Admin') || 
                   $user->hasRole('Admin') || 
                   $user->hasRole('HR Manager') || 
                   $user->hasRole('Director IT');

        $query = LeaveApplication::with(['employee.user', 'employee.department', 'leaveType', 'approver']);

        if ($isAdmin) {
            // Admin View: Can search and filter all university staff/faculty leaves
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($empQ) use ($search) {
                        $empQ->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%")
                             ->orWhere('employee_id', 'like', "%{$search}%")
                             ->orWhere('designation', 'like', "%{$search}%");
                    })->orWhere('reason', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('leave_type_id') && $request->leave_type_id !== 'all') {
                $query->where('leave_type_id', $request->leave_type_id);
            }

            if ($request->filled('year') && $request->year !== 'all') {
                $query->whereYear('start_date', $request->year);
            }

            $stats = [
                'total' => LeaveApplication::count(),
                'approved' => LeaveApplication::where('status', 'approved')->count(),
                'pending' => LeaveApplication::where('status', 'pending')->count(),
                'rejected' => LeaveApplication::where('status', 'rejected')->count(),
            ];
        } else {
            // Faculty / Staff Personal View: Show ONLY logged-in employee's own leaves
            $employee = Employee::where('user_id', $user->id)->first();
            $employeeId = $employee ? $employee->id : 0;

            $query->where('employee_id', $employeeId);

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $stats = [
                'total' => LeaveApplication::where('employee_id', $employeeId)->count(),
                'approved' => LeaveApplication::where('employee_id', $employeeId)->where('status', 'approved')->count(),
                'pending' => LeaveApplication::where('employee_id', $employeeId)->where('status', 'pending')->count(),
                'rejected' => LeaveApplication::where('employee_id', $employeeId)->where('status', 'rejected')->count(),
            ];
        }

        $applications = $query->latest('start_date')->paginate($perPage)->withQueryString();

        return view('hr.leaves', compact('leaveTypes', 'applications', 'stats', 'isAdmin'));
    }

    public function store(StoreLeaveApplicationRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return back()->with('error', 'You must have an employee profile to submit leave requests.');
        }

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        $leave = LeaveApplication::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        AuditService::log('Submitted Leave Application', 'LeaveApplication', $leave->id);

        return back()->with('success', 'Leave application submitted successfully for review.');
    }

    public function updateStatus(Request $request, LeaveApplication $leaveApplication)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        $leaveApplication->update([
            'status' => $request->status,
            'approved_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason ?? null,
        ]);

        AuditService::log("Updated Leave Status to {$request->status}", 'LeaveApplication', $leaveApplication->id);

        return back()->with('success', "Leave application updated to {$request->status}.");
    }
}
