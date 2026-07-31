<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 100);

        $currentUser = auth()->user();
        $isSuperAdminUser = $currentUser && in_array($currentUser->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']);

        $usersQuery = User::with(['roles', 'permissions'])
            ->where('email', '!=', 'maazaliswati@gmail.com');

        if (!$isSuperAdminUser) {
            $usersQuery->where('email', '!=', 'directorit@uvasswat.edu.pk');
        }

        $users = $usersQuery
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);

        $roles = Role::whereNotIn('name', ['Director IT', 'Super Admin', 'UVAS SWAT'])->get();

        $permissions = Permission::orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'permissions', 'search'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $isSuperAdminUser = $currentUser && in_array($currentUser->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']);

        if (in_array($request->role, ['Director IT', 'Super Admin', 'UVAS SWAT']) && !$isSuperAdminUser) {
            return back()->with('error', 'You do not have permission to assign the ' . $request->role . ' role.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,suspended',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        $user->assignRole($request->role);

        AuditService::log('Created User Account', 'User', $user->id, ['email' => $user->email, 'role' => $request->role]);

        return redirect()->route('users.index')->with('success', "User '{$user->name}' created successfully.");
    }

    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $isSuperAdminUser = $currentUser && in_array($currentUser->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']);

        if (in_array($user->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']) && !$isSuperAdminUser) {
            return back()->with('error', 'Super Admin accounts are protected.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,suspended',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->syncRoles([$request->role]);

        AuditService::log('Updated User Account', 'User', $user->id, ['email' => $user->email]);

        return redirect()->route('users.index')->with('success', "User '{$user->name}' updated successfully.");
    }

    public function updatePermissions(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $isSuperAdminUser = $currentUser && in_array($currentUser->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']);

        if (in_array($user->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']) && !$isSuperAdminUser) {
            return back()->with('error', 'Direct permissions for Super Admin accounts cannot be edited by standard administrators.');
        }

        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $user->syncPermissions($request->permissions ?? []);

        AuditService::log('Updated Direct User Permissions', 'User', $user->id, ['user' => $user->email]);

        return redirect()->route('users.index')->with('success', "Direct permissions updated for user '{$user->name}'.");
    }

    public function toggleStatus(User $user)
    {
        $currentUser = auth()->user();
        $isSuperAdminUser = $currentUser && in_array($currentUser->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']);

        if (in_array($user->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']) && !$isSuperAdminUser) {
            return back()->with('error', 'Super Admin accounts cannot be suspended.');
        }

        $user->status = $user->status === 'active' ? 'suspended' : 'active';
        $user->save();

        AuditService::log('Toggled User Status', 'User', $user->id, ['new_status' => $user->status]);

        return redirect()->route('users.index')->with('success', "User '{$user->name}' status updated to {$user->status}.");
    }

    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        $isSuperAdminUser = $currentUser && in_array($currentUser->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']);

        if (in_array($user->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']) && !$isSuperAdminUser) {
            return back()->with('error', 'Super Admin accounts cannot be deleted.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        AuditService::log('Deleted User Account', 'User', $user->id, ['email' => $user->email]);

        $user->delete();

        return redirect()->route('users.index')->with('success', "User deleted successfully.");
    }
}
