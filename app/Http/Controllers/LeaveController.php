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

        $query = LeaveApplication::with(['employee.user', 'employee.department', 'leaveType', 'approver']);

        // Scope filter: if regular staff wants to filter to 'mine'
        if ($request->get('scope') === 'mine') {
            $employee = Employee::where('user_id', $user->id)->first();
            $query->where('employee_id', $employee->id ?? 0);
        }

        // Search filter (Employee name, designation, reason)
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

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Leave type filter
        if ($request->filled('leave_type_id') && $request->leave_type_id !== 'all') {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        // Year filter
        if ($request->filled('year') && $request->year !== 'all') {
            $query->whereYear('start_date', $request->year);
        }

        $applications = $query->latest('start_date')->paginate($perPage)->withQueryString();

        // Calculate KPI Stats
        $stats = [
            'total' => LeaveApplication::count(),
            'approved' => LeaveApplication::where('status', 'approved')->count(),
            'pending' => LeaveApplication::where('status', 'pending')->count(),
            'rejected' => LeaveApplication::where('status', 'rejected')->count(),
        ];

        return view('hr.leaves', compact('leaveTypes', 'applications', 'stats'));
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
