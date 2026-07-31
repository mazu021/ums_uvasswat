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
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

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

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        AuditService::log('Updated Role Permissions', 'Role', $role->id, ['role' => $role->name]);

        return redirect()->route('roles.index')->with('success', "Permissions updated for role '{$role->name}'.");
    }
}
