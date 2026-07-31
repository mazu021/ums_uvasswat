<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Services\AuditService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $faculties = Faculty::with('departments.employees')->get();
        $departments = Department::with(['faculty', 'employees', 'students'])->paginate($perPage);

        return view('hr.departments', compact('faculties', 'departments'));
    }

    public function storeFaculty(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:faculties,code',
            'dean_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $faculty = Faculty::create($validated);
        AuditService::log('Created Faculty', 'Faculty', $faculty->id, ['name' => $faculty->name]);

        return back()->with('success', 'Faculty added successfully.');
    }

    public function updateFaculty(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:faculties,code,' . $faculty->id,
            'dean_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $faculty->update($validated);
        AuditService::log('Updated Faculty', 'Faculty', $faculty->id, ['name' => $faculty->name]);

        return back()->with('success', 'Faculty details updated successfully.');
    }

    public function destroyFaculty(Faculty $faculty)
    {
        AuditService::log('Deleted Faculty', 'Faculty', $faculty->id, ['name' => $faculty->name]);
        $faculty->delete();

        return back()->with('success', 'Faculty deleted successfully.');
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code',
            'hod_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department = Department::create($validated);
        AuditService::log('Created Department', 'Department', $department->id, ['name' => $department->name]);

        return back()->with('success', 'Department added successfully.');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code,' . $department->id,
            'hod_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);
        AuditService::log('Updated Department', 'Department', $department->id, ['name' => $department->name]);

        return back()->with('success', 'Department details updated successfully.');
    }

    public function destroyDepartment(Department $department)
    {
        AuditService::log('Deleted Department', 'Department', $department->id, ['name' => $department->name]);
        $department->delete();

        return back()->with('success', 'Department deleted successfully.');
    }
}
