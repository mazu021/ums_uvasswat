@extends('layouts.app')

@section('title', 'User Management')
@section('header_title', 'User & Account Management')

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false, editModalOpen: false, editUser: {} }">



    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">System Users & Credentials</h3>
            <p class="text-xs text-slate-500">Manage login accounts, toggle active/suspended status, and assign RBAC roles.</p>
        </div>
        <button @click="createModalOpen = true" class="px-4 py-2.5 bg-[#00a257] hover:bg-[#008246] text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center justify-center space-x-2">
            <i class="fa-solid fa-user-plus"></i>
            <span>Create New User</span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('users.index') }}" class="w-full flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-96 flex items-center space-x-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2e2e7f] focus:outline-none">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-lg shadow hover:bg-slate-800">Search</button>
            </div>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-600">
                <span>Show Records:</span>
                <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Per Page</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Per Page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Per Page</option>
                    <option value="100" {{ request('per_page', 100) == 100 ? 'selected' : '' }}>100 Per Page (Default)</option>
                    <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250 Per Page</option>
                    <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500 Per Page</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Users Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Assigned Role</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Last Login</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-bold text-slate-900 flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-full bg-[#2e2e7f]/10 text-[#2e2e7f] font-bold flex items-center justify-center text-xs border border-[#2e2e7f]/20">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $u->name }}</p>
                                    <p class="text-[11px] text-slate-400 font-normal">{{ $u->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-[#00a257] border border-emerald-200">
                                    {{ $u->roles->first()->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $u->phone ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($u->status === 'active')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Suspended</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-400">{{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}</td>
                            <td class="px-6 py-4 text-right space-x-1.5">
                                <!-- EDIT USER & PASSWORD BUTTON -->
                                <button type="button" 
                                        @click="editUser = {{ json_encode([
                                            'id' => $u->id,
                                            'name' => $u->name,
                                            'email' => $u->email,
                                            'phone' => $u->phone,
                                            'role' => $u->roles->first()->name ?? 'Student',
                                            'status' => $u->status,
                                            'update_url' => route('users.update', $u->id)
                                        ]) }}; editModalOpen = true" 
                                        class="px-2.5 py-1 bg-amber-100 hover:bg-amber-500 text-amber-900 hover:text-white font-bold rounded-lg text-[10px] transition flex-inline items-center space-x-1">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Edit & Password</span>
                                </button>

                                <form action="{{ route('users.toggle-status', $u->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-[10px] transition">
                                        {{ $u->status === 'active' ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>
                                @if($u->id !== auth()->id())
                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this user account?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white font-bold rounded-lg text-[10px] transition">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create User Modal -->
    <div x-show="createModalOpen" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden" @click.away="createModalOpen = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-base">Add New System User</h4>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Assign Role</label>
                        <select name="role" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Account Status</label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end space-x-2 border-t">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#00a257] hover:bg-[#008246] text-white font-bold rounded-lg shadow">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User & Change Password Modal -->
    <div x-show="editModalOpen" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden" @click.away="editModalOpen = false">
            <div class="px-6 py-4 bg-[#2e2e7f] text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-user-pen text-amber-400 text-lg"></i>
                    <h4 class="font-bold text-base">Edit User Account & Password</h4>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form :action="editUser.update_url" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" x-model="editUser.name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#2e2e7f] focus:outline-none font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" x-model="editUser.email" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#2e2e7f] focus:outline-none font-semibold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" x-model="editUser.phone" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#2e2e7f] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Assigned Role</label>
                        <select name="role" x-model="editUser.role" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#2e2e7f] focus:outline-none font-semibold">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Account Status</label>
                    <select name="status" x-model="editUser.status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-[#2e2e7f] focus:outline-none font-bold">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                <!-- Password Change Box -->
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl space-y-1.5">
                    <label class="block font-extrabold text-amber-900 flex items-center">
                        <i class="fa-solid fa-key me-1.5 text-amber-600"></i> Change Password
                    </label>
                    <p class="text-[11px] text-amber-800 leading-snug">
                        Type a new password below to automatically replace the old password, or leave empty to keep current password.
                    </p>
                    <input type="password" name="password" minlength="6" placeholder="Enter new password (optional)" 
                           class="w-full px-3 py-2 bg-white border border-amber-300 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="pt-4 flex justify-end space-x-2 border-t">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-[#2e2e7f] hover:bg-[#232363] text-white font-extrabold rounded-lg shadow">Update Account & Password</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
