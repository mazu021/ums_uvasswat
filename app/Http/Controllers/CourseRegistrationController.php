<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseRegistrationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = Student::with(['department'])->where('user_id', $user->id)->first();

        if (!$student && !$user->hasAnyRole(['Super Admin', 'University Admin', 'HOD', 'Teacher / Lecturer'])) {
            return back()->with('error', 'Student profile not found.');
        }

        if ($student) {
            $availableCourses = Course::where('status', 'active')->get();
            $registrations = CourseRegistration::with(['course', 'semester'])
                ->where('student_id', $student->id)
                ->get();
            
            $enrolledCreditHours = $registrations->where('status', 'approved')->sum(fn($r) => $r->course->credit_hours ?? 3);

            return view('academics.course_registration', compact('student', 'availableCourses', 'registrations', 'enrolledCreditHours'));
        }

        // HOD / Admin View
        $perPage = $request->get('per_page', 100);
        $pendingRegistrations = CourseRegistration::with(['student.user', 'course'])
            ->where('status', 'pending')
            ->latest()
            ->paginate($perPage);

        return view('academics.course_approval', compact('pendingRegistrations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $existing = CourseRegistration::where('student_id', $student->id)
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already requested registration for this course.');
        }

        CourseRegistration::create([
            'student_id' => $student->id,
            'course_id' => $validated['course_id'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Course registration requested successfully. Pending approval by HOD.');
    }

    public function updateStatus(Request $request, CourseRegistration $registration)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,dropped',
            'remarks' => 'nullable|string',
        ]);

        $registration->update([
            'status' => $request->status,
            'approved_by' => Auth::user()->name,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Course registration status updated.');
    }
}
