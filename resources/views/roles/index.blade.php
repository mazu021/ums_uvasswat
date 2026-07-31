@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('header_title', 'Role-Based Access Control (RBAC)')

@section('content')
<div class="space-y-6" x-data="{ createRoleModal: false, editRoleModal: false, permModalOpen: false, editRole: {}, permRole: {} }">

    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Roles & Access Control</h3>
            <p class="text-xs text-slate-500">Configure system roles and granular permissions for Super Admin, HR, Finance, HOD, Faculty, and Student roles.</p>
        </div>
        <button @click="createRoleModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center space-x-2">
            <i class="fa-solid fa-plus me-1"></i>
            <span>Add Custom Role</span>
        </button>
    </div>

    <!-- System Roles Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h4 class="font-extrabold text-sm text-slate-900 flex items-center space-x-2">
                <i class="fa-solid fa-user-shield text-emerald-600"></i>
                <span>Defined System Roles</span>
            </h4>
            <span class="text-xs font-bold text-slate-400">{{ $roles->count() }} System Roles</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">S.No</th>
                        <th class="px-6 py-3.5">Role Name</th>
                        <th class="px-6 py-3.5">Assigned Permissions</th>
                        <th class="px-6 py-3.5">Guard Name</th>
                        <th class="px-6 py-3.5 text-center w-40">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roles as $role)
                        @php
                            $rolePerms = $role->permissions->pluck('name')->toArray();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-4 text-center font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900 flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-bold text-sm">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div>
                                    <p class="font-extrabold text-sm text-slate-900">{{ $role->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-normal">System Access Role</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-slate-100 text-slate-800 font-extrabold text-[11px] rounded-full border border-slate-200">
                                    {{ count($rolePerms) }} Permissions
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px] text-slate-500 font-bold">web</td>
                            <!-- Action Block (Matches exact UI design) -->
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex rounded-xl shadow-xs border border-slate-300 overflow-hidden divide-x divide-slate-200 bg-white">
                                    
                                    <!-- Edit Role Name Button -->
                                    <button type="button" 
                                            @click="editRole = {{ json_encode(['id' => $role->id, 'name' => $role->name, 'update_url' => route('roles.update', $role->id)]) }}; editRoleModal = true"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800 transition" 
                                            title="Edit Role Name">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>

                                    <!-- Assign Permissions Popup Button (Key Icon) -->
                                    <button type="button" 
                                            @click="permRole = {{ json_encode([
                                                'id' => $role->id,
                                                'name' => $role->name,
                                                'permissions' => $rolePerms,
                                                'update_url' => route('roles.update-permissions', $role->id)
                                            ]) }}; permModalOpen = true"
                                            class="p-2 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-800 transition" 
                                            title="Assign Permissions Popup">
                                        <i class="fa-solid fa-key text-xs"></i>
                                    </button>

                                    <!-- Delete Role Button -->
                                    @if(!in_array($role->name, ['Super Admin', 'Admin', 'Faculty', 'Student']))
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete role {{ $role->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 hover:text-rose-800 transition" title="Delete Role">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="p-2 text-slate-300 cursor-not-allowed" title="Default System Role Locked">
                                            <i class="fa-solid fa-lock text-xs"></i>
                                        </button>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">No roles defined.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assign Permissions Popup Modal -->
    <div x-show="permModalOpen" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="permModalOpen = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-white">Assign Role Permissions</h4>
                        <p class="text-[10px] text-slate-300" x-text="'Role: ' + permRole.name"></p>
                    </div>
                </div>
                <button @click="permModalOpen = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="permRole.update_url" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-900 flex items-center space-x-3">
                    <i class="fa-solid fa-circle-info text-emerald-600 text-base"></i>
                    <p class="text-xs font-medium">Select granular system permissions to assign to role <strong x-text="permRole.name"></strong>.</p>
                </div>

                <!-- Permissions Checkboxes Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 max-h-80 overflow-y-auto p-2 border border-slate-200 rounded-2xl bg-slate-50/50">
                    @foreach($permissions as $perm)
                        <label class="flex items-center space-x-2 p-2 bg-white rounded-xl border border-slate-200 hover:border-emerald-400 transition cursor-pointer shadow-2xs">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                :checked="permRole.permissions && permRole.permissions.includes('{{ $perm->name }}')"
                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-slate-800 font-bold text-[11px] truncate">{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="pt-3 flex items-center justify-between border-t border-slate-100">
                    <button type="button" @click="permModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow transition flex items-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Role Permissions</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div x-show="createRoleModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="createRoleModal = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <h4 class="font-extrabold text-sm flex items-center gap-2">
                    <i class="fa-solid fa-plus text-emerald-400"></i>
                    <span>Add New Custom Role</span>
                </h4>
                <button @click="createRoleModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role Title *</label>
                    <input type="text" name="name" placeholder="e.g. Lab Manager, Academic Coordinator" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="createRoleModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow transition">Create Role</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div x-show="editRoleModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="editRoleModal = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <h4 class="font-extrabold text-sm flex items-center gap-2">
                    <i class="fa-solid fa-pen text-emerald-400"></i>
                    <span>Edit Role Name</span>
                </h4>
                <button @click="editRoleModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form :action="editRole.update_url" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role Name *</label>
                    <input type="text" name="name" x-model="editRole.name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="editRoleModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow transition">Save Role Name</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
