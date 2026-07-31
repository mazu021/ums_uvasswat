<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\Program;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        $programs = Program::with('department')->orderBy('name')->get();
        $selectedProgramId = $request->get('program_id', $programs->first()?->id);

        $curriculums = Curriculum::with(['program', 'curriculumCourses.course'])
            ->when($selectedProgramId, function ($query) use ($selectedProgramId) {
                return $query->where('program_id', $selectedProgramId);
            })
            ->get();

        $activeCurriculum = $curriculums->where('status', 'active')->first() ?? $curriculums->first();
        $courses = Course::orderBy('course_code')->get();

        return view('academics.curriculum.index', compact(
            'programs',
            'selectedProgramId',
            'curriculums',
            'activeCurriculum',
            'courses'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'effective_year' => 'required|integer|min:2000|max:2100',
            'total_semesters' => 'required|integer|min:1|max:12',
            'total_credit_hours' => 'required|integer|min:1',
            'status' => 'required|in:active,archived,draft',
        ]);

        Curriculum::create($validated);

        return redirect()->back()->with('success', 'Curriculum (Study Scheme) created successfully!');
    }

    public function addCourse(Request $request, Curriculum $curriculum)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_number' => 'required|integer|min:1|max:12',
            'course_type' => 'required|in:core,elective,general,lab,project',
            'credit_hours' => 'required|integer|min:1|max:10',
        ]);

        $exists = CurriculumCourse::where('curriculum_id', $curriculum->id)
            ->where('course_id', $validated['course_id'])
            ->where('semester_number', $validated['semester_number'])
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'This course is already added to Semester ' . $validated['semester_number'] . ' in this Curriculum.');
        }

        CurriculumCourse::create([
            'curriculum_id' => $curriculum->id,
            'course_id' => $validated['course_id'],
            'semester_number' => $validated['semester_number'],
            'course_type' => $validated['course_type'],
            'credit_hours' => $validated['credit_hours'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Course added to Curriculum successfully!');
    }

    public function removeCourse(CurriculumCourse $curriculumCourse)
    {
        $curriculumCourse->delete();
        return redirect()->back()->with('success', 'Course removed from Curriculum successfully.');
    }
}
