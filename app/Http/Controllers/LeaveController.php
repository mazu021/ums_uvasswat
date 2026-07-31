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
        $perPage = $request->get('per_page', 100);

        if ($user->hasRole('HR Manager') || $user->hasRole('Super Admin')) {
            $applications = LeaveApplication::with(['employee.department', 'leaveType', 'approver'])
                ->latest()
                ->paginate($perPage);
        } else {
            $employee = Employee::where('user_id', $user->id)->first();
            $applications = LeaveApplication::with(['employee', 'leaveType', 'approver'])
                ->where('employee_id', $employee->id ?? 0)
                ->latest()
                ->paginate($perPage);
        }

        return view('hr.leaves', compact('leaveTypes', 'applications'));
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
