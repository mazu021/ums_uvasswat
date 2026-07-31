<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSuperAdminUser = $user && in_array($user->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']);

        $rolesQuery = Role::with('permissions');
        
        // Hide Director IT & Super Admin roles from regular admins
        if (!$isSuperAdminUser) {
            $rolesQuery->whereNotIn('name', ['Director IT', 'Super Admin', 'UVAS SWAT']);
        }

        $roles = $rolesQuery->get();
        $permissions = Permission::orderBy('name')->get();

        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        AuditService::log('Created Role', 'Role', $role->id, ['role' => $role->name]);

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' created successfully.");
    }

    public function update(Request $request, Role $role)
    {
        $user = auth()->user();
        $isSuperAdminUser = $user && in_array($user->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']);

        if (in_array($role->name, ['Director IT', 'Super Admin', 'UVAS SWAT']) && !$isSuperAdminUser) {
            return back()->with('error', 'Role ' . $role->name . ' is a system root role and cannot be edited.');
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);

        $oldName = $role->name;
        $role->update(['name' => $request->name]);

        AuditService::log('Updated Role Name', 'Role', $role->id, ['old' => $oldName, 'new' => $role->name]);

        return redirect()->route('roles.index')->with('success', "Role updated successfully.");
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $user = auth()->user();
        $isSuperAdminUser = $user && in_array($user->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']);

        if (in_array($role->name, ['Director IT', 'Super Admin', 'UVAS SWAT']) && !$isSuperAdminUser) {
            return back()->with('error', 'Role ' . $role->name . ' permissions can only be managed by Super Admin.');
        }

        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        AuditService::log('Updated Role Permissions', 'Role', $role->id, ['role' => $role->name]);

        return redirect()->route('roles.index')->with('success', "Permissions updated for role '{$role->name}'.");
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Director IT', 'Super Admin', 'UVAS SWAT', 'University Admin', 'Faculty', 'Student'])) {
            return back()->with('error', "Default system role '{$role->name}' cannot be deleted.");
        }

        AuditService::log('Deleted Role', 'Role', $role->id, ['role' => $role->name]);
        $role->delete();

        return redirect()->route('roles.index')->with('success', "Role deleted successfully.");
    }
}
