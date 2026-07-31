@extends('layouts.app')

@section('title', 'User Management')
@section('header_title', 'User & Account Management')

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false, editModalOpen: false, editUser: {} }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">System Users & Credentials</h3>
            <p class="text-xs text-slate-500">Manage login accounts, toggle active/suspended status, and edit user profile details.</p>
        </div>
        <button @click="createModalOpen = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center justify-center space-x-2">
            <i class="fa-solid fa-user-plus"></i>
            <span>Create New User</span>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('users.index') }}" class="w-full flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-96 flex items-center space-x-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl shadow hover:bg-slate-800 transition">Search</button>
            </div>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-600">
                <span>Show Records:</span>
                <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
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
                        <th class="px-4 py-3.5 text-center w-12">S.No</th>
                        <th class="px-6 py-3.5">User Details</th>
                        <th class="px-6 py-3.5">Assigned Role</th>
                        <th class="px-6 py-3.5">Phone</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-center w-36">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- S.No Column -->
                            <td class="px-4 py-4 text-center font-bold text-slate-400">
                                {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                            </td>
                            <!-- User Details -->
                            <td class="px-6 py-4 font-bold text-slate-900 flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs shadow-xs border border-slate-700">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $u->name }}</p>
                                    <p class="text-[11px] text-slate-400 font-normal">{{ $u->email }}</p>
                                </div>
                            </td>
                            <!-- Assigned Role -->
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $u->roles->first()->name ?? 'No Role' }}
                                </span>
                            </td>
                            <!-- Phone -->
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $u->phone ?? 'N/A' }}</td>
                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($u->status === 'active')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Suspended</span>
                                @endif
                            </td>
                            <!-- Compact Action Buttons Block (Matches exact UI design) -->
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex rounded-xl shadow-xs border border-slate-300 overflow-hidden divide-x divide-slate-200 bg-white">
                                    
                                    <!-- Edit User & Password Button -->
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
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800 transition" 
                                            title="Edit User & Password">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>

                                    <!-- Toggle Active/Suspend Status Button -->
                                    <form action="{{ route('users.toggle-status', $u->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="p-2 {{ $u->status === 'active' ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} transition" 
                                                title="{{ $u->status === 'active' ? 'Suspend Account' : 'Activate Account' }}">
                                            <i class="fa-solid {{ $u->status === 'active' ? 'fa-lock' : 'fa-lock-open' }} text-xs"></i>
                                        </button>
                                    </form>

                                    <!-- Delete Button -->
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete user {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 hover:text-rose-800 transition" title="Delete User">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-semibold">No users found.</td>
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
    <div x-show="createModalOpen" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="createModalOpen = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <h4 class="font-extrabold text-sm flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-emerald-400"></i>
                    <span>Add New System User</span>
                </h4>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address *</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Password *</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Assign Role *</label>
                        <select name="role" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Account Status</label>
                        <select name="status" class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end space-x-2 border-t">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow transition">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User & Change Password Modal -->
    <div x-show="editModalOpen" x-transition class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="editModalOpen = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-user-pen text-emerald-400 text-base"></i>
                    <h4 class="font-bold text-sm">Edit User Account & Password</h4>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form :action="editUser.update_url" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" x-model="editUser.name" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500 font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" x-model="editUser.email" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500 font-semibold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Role</label>
                        <select name="role" x-model="editUser.role" required class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500 font-semibold">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" x-model="editUser.status" class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500 font-semibold">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" x-model="editUser.phone" class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500 font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">New Password (Optional)</label>
                        <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full px-3 py-2 border rounded-xl focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="pt-4 flex justify-end space-x-2 border-t">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
