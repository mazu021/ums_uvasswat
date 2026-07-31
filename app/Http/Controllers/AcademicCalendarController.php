<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use App\Models\AcademicSession;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index(Request $request)
    {
        $allCalendars = AcademicCalendar::with('uploader')->latest()->get();
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();

        $selectedSession = $request->get('session');

        if ($selectedSession) {
            $selectedCalendar = AcademicCalendar::where('session_name', $selectedSession)->latest()->first();
        } else {
            $selectedCalendar = AcademicCalendar::where('is_active', true)->latest()->first() 
                ?? AcademicCalendar::latest()->first();
        }

        return view('academics.academic_calendar', compact(
            'allCalendars',
            'academicSessions',
            'selectedCalendar',
            'selectedSession'
        ));
    }

    public function upload(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin') && !$user->can('manage settings')) {
            return back()->with('error', 'Only System Administrators can upload the official Academic Calendar.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'session_name' => 'required|string|max:100',
            'calendar_file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        if ($request->hasFile('calendar_file')) {
            $file = $request->file('calendar_file');
            $path = $file->store('academic_calendars', 'public');

            // Deactivate previous active calendars if needed
            AcademicCalendar::where('session_name', $request->session_name)->update(['is_active' => false]);

            $calendar = AcademicCalendar::create([
                'title' => $request->title,
                'session_name' => $request->session_name,
                'file_path' => $path,
                'is_active' => true,
                'uploaded_by' => $user->id,
            ]);

            AuditService::log('Uploaded Academic Calendar', 'AcademicCalendar', $calendar->id, [
                'title' => $request->title,
                'session' => $request->session_name,
                'file' => $path
            ]);
        }

        return back()->with('success', "Academic Calendar for Session {$request->session_name} uploaded successfully!");
    }

    public function destroy(AcademicCalendar $academicCalendar)
    {
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin')) {
            return back()->with('error', 'Unauthorized.');
        }

        $session = $academicCalendar->session_name;
        $academicCalendar->delete();

        return back()->with('success', "Academic Calendar record for {$session} deleted.");
    }
}
