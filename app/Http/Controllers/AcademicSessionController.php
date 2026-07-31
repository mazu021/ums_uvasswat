<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $sessions = AcademicSession::latest()->paginate($perPage);

        return view('academics.academic_sessions', compact('sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_sessions,name',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,closed',
        ]);

        if ($validated['status'] === 'active') {
            AcademicSession::query()->update(['status' => 'inactive']);
        }

        $session = AcademicSession::create($validated);
        AuditService::log('Created Academic Session', 'AcademicSession', $session->id, ['name' => $session->name]);

        return back()->with('success', 'Academic session created successfully.');
    }

    public function update(Request $request, AcademicSession $academicSession)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_sessions,name,' . $academicSession->id,
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,closed',
        ]);

        if ($validated['status'] === 'active' && $academicSession->status !== 'active') {
            AcademicSession::where('id', '!=', $academicSession->id)->update(['status' => 'inactive']);
        }

        $academicSession->update($validated);
        AuditService::log('Updated Academic Session', 'AcademicSession', $academicSession->id, ['name' => $academicSession->name]);

        return back()->with('success', 'Academic session updated successfully.');
    }

    public function updateStatus(AcademicSession $academicSession, Request $request)
    {
        if ($request->status === 'active') {
            AcademicSession::where('id', '!=', $academicSession->id)->update(['status' => 'inactive']);
        }

        $academicSession->update(['status' => $request->status]);
        AuditService::log('Updated Academic Session Status', 'AcademicSession', $academicSession->id, ['status' => $request->status]);

        return back()->with('success', 'Academic session status updated.');
    }

    public function destroy(AcademicSession $academicSession)
    {
        $name = $academicSession->name;
        $academicSession->delete();
        AuditService::log('Deleted Academic Session', 'AcademicSession', $academicSession->id, ['name' => $name]);

        return back()->with('success', 'Academic session deleted successfully.');
    }
}
