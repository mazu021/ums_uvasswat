<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Announcement::with('creator');
        $perPage = $request->get('per_page', 100);

        $isAuthorizedAdmin = $user && (
            $user->hasRole('Super Admin') ||
            $user->hasRole('Director IT') ||
            $user->hasRole('Admin') ||
            $user->hasRole('UVAS SWAT') ||
            in_array($user->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk'])
        );

        // Non-admin roles see published announcements meant for everyone or their target role
        if (!$isAuthorizedAdmin) {
            $query->where('is_published', true);

            $targetRoles = ['all'];
            if ($user->hasRole('Student')) {
                $targetRoles[] = 'student';
            }
            if ($user->hasRole('Faculty') || $user->hasRole('Teacher')) {
                $targetRoles[] = 'faculty';
            }
            if ($user->hasRole('Staff') || $user->hasRole('HR')) {
                $targetRoles[] = 'staff';
            }

            $query->whereIn('target_role', $targetRoles);
        }

        $announcements = $query->latest('published_at')->paginate($perPage);

        return view('utilities.announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_role' => 'required|string',
            'priority' => 'required|in:normal,high,urgent',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_published'] = true;
        $validated['published_at'] = now();

        $announcement = Announcement::create($validated);
        AuditService::log('Published Announcement', 'Announcement', $announcement->id, ['title' => $announcement->title]);

        return back()->with('success', 'Announcement published successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        AuditService::log('Deleted Announcement', 'Announcement', $announcement->id);
        $announcement->delete();

        return back()->with('success', 'Announcement removed successfully.');
    }
}
