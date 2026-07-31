<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\Department;
use App\Models\Employee;
use App\Services\AuditService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $courses = Course::with(['department', 'offerings.teacher', 'assignments.faculty', 'curriculumCourses.curriculum.program'])->paginate($perPage);
        $departments = Department::all();
        $faculties = Employee::where('type', 'faculty')->get();
        $programs = \App\Models\Program::where('status', 'active')->with('department')->get();
        $batches = \App\Models\Batch::all();
        $sections = \App\Models\Section::all();
        $academicSessions = \App\Models\AcademicSession::all();

        return view('academics.courses', compact(
            'courses',
            'departments',
            'faculties',
            'programs',
            'batches',
            'sections',
            'academicSessions'
        ));
    }

    public function store(StoreCourseRequest $request)
    {
        $validated = $request->validated();
        $course = Course::create($validated);

        AuditService::log('Created Course', 'Course', $course->id, ['code' => $course->course_code]);

        return back()->with('success', 'Course added to catalog successfully.');
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'title' => 'required|string|max:255',
            'credit_hours' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:12',
            'description' => 'nullable|string',
        ]);

        $course->update($validated);

        AuditService::log('Updated Course', 'Course', $course->id, ['code' => $course->course_code]);

        return back()->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->offerings()->delete();
        $course->assignments()->delete();
        $course->delete();

        AuditService::log('Deleted Course', 'Course', $course->id);

        return back()->with('success', 'Course deleted from catalog successfully.');
    }

    public function assignFaculty(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'employee_id' => 'required|exists:employees,id',
            'program_id' => 'required|exists:programs,id',
            'batch_id' => 'nullable|exists:batches,id',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'academic_session' => 'nullable|string',
            'semester' => 'required|integer',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $employee = Employee::find($validated['employee_id']);
        if (!$employee) {
            return back()->with('error', 'Faculty member not found.');
        }

        $user = $employee->user;
        if (!$user && $employee->email) {
            $user = \App\Models\User::where('email', $employee->email)->first();
        }
        if (!$user && $employee->first_name) {
            $user = \App\Models\User::where('name', 'like', "%{$employee->first_name}%")->first();
        }

        if (!$user) {
            return back()->with('error', 'User account for faculty member not linked.');
        }

        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }

        $academicSessionId = $validated['academic_session_id'] 
            ?? (\App\Models\AcademicSession::where('status', 'active')->value('id') ?? 1);

        $programId = $validated['program_id'];

        $batchId = !empty($validated['batch_id']) 
            ? $validated['batch_id'] 
            : (\App\Models\Batch::where('program_id', $programId)->value('id') 
                ?? \App\Models\Batch::firstOrCreate([
                    'code' => 'BATCH-' . $programId . '-F26',
                ], [
                    'program_id' => $programId,
                    'academic_session_id' => $academicSessionId,
                    'name' => 'Default Academic Batch',
                    'status' => 'active',
                ])->id);

        // Allow multiple offerings for the same teacher & course across different programs / batches!
        \App\Models\CourseOffering::firstOrCreate([
            'course_id' => $validated['course_id'],
            'teacher_id' => $user->id,
            'program_id' => $programId,
            'batch_id' => $batchId,
            'section_id' => $validated['section_id'] ?? null,
            'academic_session_id' => $academicSessionId,
        ], [
            'semester_number' => $validated['semester'],
            'status' => 'active',
        ]);

        CourseAssignment::firstOrCreate([
            'course_id' => $validated['course_id'],
            'employee_id' => $employee->id,
            'semester' => $validated['semester'],
        ], [
            'academic_session' => '2025-2026',
        ]);

        AuditService::log('Assigned Faculty to Course Offering', 'CourseOffering', $validated['course_id']);

        return back()->with('success', 'Faculty member assigned to program course offering successfully.');
    }

    public function unassignFaculty(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_id' => 'nullable|integer',
            'employee_id' => 'nullable|integer',
            'offering_id' => 'nullable|integer',
        ]);

        $courseId = $validated['course_id'];
        $offeringId = $validated['offering_id'] ?? null;
        $userId = $validated['user_id'] ?? null;
        $employeeId = $validated['employee_id'] ?? null;

        if ($offeringId) {
            \App\Models\CourseOffering::where('id', $offeringId)->delete();
        } elseif ($userId) {
            \App\Models\CourseOffering::where('course_id', $courseId)
                ->where('teacher_id', $userId)
                ->delete();
        }

        if ($employeeId) {
            CourseAssignment::where('course_id', $courseId)
                ->where('employee_id', $employeeId)
                ->delete();
        }

        AuditService::log('Unassigned Faculty from Course Offering', 'Course', $courseId);

        return back()->with('success', 'Faculty member unassigned from course offering successfully.');
    }
}
