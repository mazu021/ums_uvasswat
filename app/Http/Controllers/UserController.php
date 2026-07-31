<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 100);
        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage);

        $roles = Role::all();

        return view('users.index', compact('users', 'roles', 'search'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);

        AuditService::log('Created User', 'User', $user->id, ['name' => $user->name, 'email' => $user->email]);

        return redirect()->route('users.index')->with('success', 'User account created successfully.');
    }

    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        AuditService::log("Updated User Status to {$newStatus}", 'User', $user->id);

        return back()->with('success', "User account {$user->name} status changed to {$newStatus}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        AuditService::log('Deleted User', 'User', $user->id, ['email' => $user->email]);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User account deleted successfully.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,suspended,inactive',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        AuditService::log('Updated User Profile & Password', 'User', $user->id, [
            'name' => $user->name,
            'password_changed' => !empty($validated['password']),
        ]);

        $msg = !empty($validated['password'])
            ? "User details and password for {$user->name} updated successfully!"
            : "User details for {$user->name} updated successfully!";

        return redirect()->route('users.index')->with('success', $msg);
    }
}
