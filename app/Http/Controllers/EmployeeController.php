<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');

        $perPage = $request->get('per_page', 100);
        $employees = Employee::with('department')
            ->where('email', '!=', 'maazaliswati@gmail.com')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('employee_code', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->latest()
            ->paginate($perPage);

        $departments = Department::all();

        return view('hr.employees', compact('employees', 'departments', 'search', 'type'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();

        // Check if user account already exists, or create a new user account
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            $user = User::create([
                'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                'email' => $validated['email'],
                'password' => Hash::make('Password123!'),
                'phone' => $validated['phone'] ?? null,
                'status' => 'active',
            ]);

            $roleName = $validated['type'] === 'faculty' ? 'Faculty' : 'HR Manager';
            $user->assignRole($roleName);
        } else {
            if (!$user->phone && !empty($validated['phone'])) {
                $user->update(['phone' => $validated['phone']]);
            }
            $roleName = $validated['type'] === 'faculty' ? 'Faculty' : 'HR Manager';
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }

        $validated['user_id'] = $user->id;
        $employee = Employee::create($validated);

        AuditService::log('Created Employee Profile', 'Employee', $employee->id, ['code' => $employee->employee_code]);

        return back()->with('success', 'Employee profile created successfully and linked to user account.');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'employee_code' => 'required|string|unique:employees,employee_code,' . $employee->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:20',
            'designation' => 'required|string|max:255',
            'type' => 'required|in:faculty,staff,administration',
            'basic_salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,on_leave,suspended,resigned',
        ]);

        $employee->update($validated);

        if ($employee->user) {
            $employee->user->update([
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);
        }

        AuditService::log('Updated Employee Profile', 'Employee', $employee->id, ['code' => $employee->employee_code]);

        return back()->with('success', 'Employee profile updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        AuditService::log('Deleted Employee Profile', 'Employee', $employee->id, ['code' => $employee->employee_code]);
        if ($employee->user) {
            $employee->user->delete();
        } else {
            $employee->delete();
        }

        return back()->with('success', 'Employee profile deleted successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'attendances', 'leaveApplications.leaveType', 'payrolls', 'courseAssignments.course']);
        return view('hr.employee_show', compact('employee'));
    }
}
