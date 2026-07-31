@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('header_title', 'Role-Based Access Control (RBAC)')

@section('content')
<div class="space-y-6" x-data="{ createRoleModal: false }">

    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Roles & Permission Matrix</h3>
            <p class="text-xs text-slate-500">Configure granular system permissions for Super Admin, HR, Finance, HOD, Faculty, and Student roles.</p>
        </div>
        <button @click="createRoleModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">
            <i class="fa-solid fa-plus me-1"></i> Add Custom Role
        </button>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($roles as $role)
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <h4 class="font-bold text-base text-slate-900">{{ $role->name }}</h4>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-0.5 bg-slate-100 text-slate-600 rounded-full">
                        {{ $role->permissions->count() }} Permissions
                    </span>
                </div>

                <!-- Permissions Checkbox Form -->
                <form action="{{ route('roles.update-permissions', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-2 text-xs py-2 max-h-48 overflow-y-auto pr-2">
                        @foreach($permissions as $perm)
                            <label class="flex items-center space-x-2 p-1.5 rounded hover:bg-slate-50 border border-transparent hover:border-slate-200">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                    {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-slate-700 font-medium">{{ $perm->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="pt-3 border-t flex justify-end">
                        <button type="submit" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg transition">
                            Save Matrix
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>

    <!-- Create Role Modal -->
    <div x-show="createRoleModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="createRoleModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Add New Custom Role</h4>
                <button @click="createRoleModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role Title</label>
                    <input type="text" name="name" placeholder="e.g. Lab Technician" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="createRoleModal = false" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white font-bold rounded-lg shadow">Create Role</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
