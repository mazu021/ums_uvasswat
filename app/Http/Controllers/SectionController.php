<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Program;
use App\Models\Section;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $sections = Section::with(['batch', 'program'])->latest()->paginate($perPage);
        $programs = Program::where('status', 'active')->get();
        $batches = Batch::all();

        return view('academics.sections', compact('sections', 'programs', 'batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'program_id' => 'nullable|exists:programs,id',
            'batch_id' => 'nullable|exists:batches,id',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $section = Section::create($validated);
        AuditService::log('Created Class Section', 'Section', $section->id, ['name' => $section->name]);

        return back()->with('success', 'Class section created successfully.');
    }
}
