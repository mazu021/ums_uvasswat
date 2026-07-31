<?php

namespace App\Http\Controllers;

use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\Timetable;
use App\Services\AuditService;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $timetables = Timetable::with(['courseOffering.course', 'courseOffering.teacher', 'program'])
            ->latest()
            ->paginate($perPage);

        $offerings = CourseOffering::with(['course', 'teacher'])->get();
        $programs = Program::where('status', 'active')->get();

        return view('academics.timetable', compact('timetables', 'offerings', 'programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_number' => 'nullable|string',
        ]);

        $timetable = Timetable::create($validated);
        AuditService::log('Created Timetable Slot', 'Timetable', $timetable->id);

        return back()->with('success', 'Class timetable slot created successfully.');
    }
}
