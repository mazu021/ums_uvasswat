<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\Student;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $scholarships = Scholarship::with(['student.user', 'student.department'])
            ->latest()
            ->paginate($perPage);
        $students = Student::with('user')->where('status', 'active')->get();

        return view('finance.scholarships.index', compact('scholarships', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'sponsor_name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'awarded_amount' => 'required|numeric|min:0',
        ]);

        Scholarship::create([
            'student_id' => $validated['student_id'],
            'title' => $validated['title'],
            'sponsor_name' => $validated['sponsor_name'],
            'discount_percentage' => $validated['discount_percentage'],
            'awarded_amount' => $validated['awarded_amount'],
            'status' => 'awarded',
        ]);

        return back()->with('success', 'Scholarship awarded successfully.');
    }

    public function updateStatus(Request $request, Scholarship $scholarship)
    {
        $request->validate([
            'status' => 'required|in:applied,under_review,awarded,rejected',
            'remarks' => 'nullable|string',
        ]);

        $scholarship->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Scholarship status updated.');
    }
}
