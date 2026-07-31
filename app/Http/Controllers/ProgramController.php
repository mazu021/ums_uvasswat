<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Program;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $programs = Program::where('status', 'active')->with(['department.faculty'])->latest()->paginate($perPage);
        $departments = Department::all();

        return view('academics.programs', compact('programs', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code',
            'department_id' => 'required|exists:departments,id',
            'degree_level' => 'required|string',
            'total_semesters' => 'required|integer|min:1|max:12',
            'status' => 'required|in:active,inactive',
        ]);

        $program = Program::create($validated);
        AuditService::log('Created Program', 'Program', $program->id, ['code' => $program->code]);

        return back()->with('success', 'Degree program created successfully.');
    }
}
