<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentIdCardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $departmentId = $request->get('department_id');
        $semester = $request->get('semester');

        $query = Student::with('department')
            ->where('status', 'active');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        if (!empty($departmentId)) {
            $query->where('department_id', $departmentId);
        }

        if (!empty($semester)) {
            $query->where('current_semester', $semester);
        }

        $students = $query->orderBy('first_name')->get();
        $departments = Department::all();

        return view('academics.student_id_cards', compact('students', 'departments', 'search', 'departmentId', 'semester'));
    }
}
