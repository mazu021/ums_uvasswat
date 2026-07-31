@extends('layouts.app')

@section('title', 'Academic Sessions Management')
@section('header_title', 'Academic Sessions & Term Management')

@section('content')
<div class="space-y-6" x-data="{ 
    createModal: false, 
    editModal: false, 
    deleteModal: false,
    editSession: { id: null, name: '', start_date: '', end_date: '', status: 'active' },
    deleteSession: { id: null, name: '' }
}">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Academic Sessions</h3>
            <p class="text-xs text-slate-500">Manage university academic years, active terms (e.g. 2024-2025, 2025-2026, 2026-2027), and session statuses.</p>
        </div>
        <button @click="createModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
            <i class="fa-solid fa-calendar-plus"></i>
            <span>Add Academic Session</span>
        </button>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-2xl flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Academic Session</th>
                        <th class="px-6 py-4">Start Date</th>
                        <th class="px-6 py-4">End Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $sess)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-bold text-slate-900 flex items-center space-x-2">
                                <i class="fa-solid fa-calendar-check text-emerald-600 text-sm"></i>
                                <span>{{ $sess->name }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $sess->start_date ? $sess->start_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $sess->end_date ? $sess->end_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $sess->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($sess->status === 'closed' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                    {{ $sess->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Edit Button -->
                                    <button @click="editSession = { 
                                        id: {{ $sess->id }}, 
                                        name: '{{ addslashes($sess->name) }}', 
                                        start_date: '{{ $sess->start_date ? $sess->start_date->format('Y-m-d') : '' }}', 
                                        end_date: '{{ $sess->end_date ? $sess->end_date->format('Y-m-d') : '' }}', 
                                        status: '{{ $sess->status }}' 
                                    }; editModal = true" 
                                    class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white font-bold rounded-lg text-xs transition flex items-center space-x-1" 
                                    title="Edit Session">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('academics.sessions.update-status', $sess->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $sess->status === 'active' ? 'inactive' : 'active' }}">
                                        <button type="submit" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs transition">
                                            Toggle {{ $sess->status === 'active' ? 'Inactive' : 'Active' }}
                                        </button>
                                    </form>

                                    <!-- Delete Button -->
                                    <button @click="deleteSession = { id: {{ $sess->id }}, name: '{{ addslashes($sess->name) }}' }; deleteModal = true"
                                    class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white font-bold rounded-lg text-xs transition flex items-center space-x-1" 
                                    title="Delete Session">
                                        <i class="fa-solid fa-trash-can"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs">No academic sessions recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="createModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="createModal = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-calendar-plus text-emerald-400"></i>
                    <h4 class="font-bold text-sm">Create New Academic Session</h4>
                </div>
                <button @click="createModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('academics.sessions.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Session Name *</label>
                    <input type="text" name="name" required placeholder="e.g. 2026 - 2027" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">End Date</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Session Status *</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="active">Active Current Session</option>
                        <option value="inactive">Inactive / Upcoming</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl shadow hover:bg-emerald-700 transition">Save Session</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="editModal = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-pen-to-square text-blue-400"></i>
                    <h4 class="font-bold text-sm">Edit Academic Session</h4>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form :action="'{{ url('/academics/sessions') }}/' + editSession.id" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Session Name *</label>
                    <input type="text" name="name" x-model="editSession.name" required placeholder="e.g. 2026 - 2027" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" x-model="editSession.start_date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">End Date</label>
                        <input type="date" name="end_date" x-model="editSession.end_date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Session Status *</label>
                    <select name="status" x-model="editSession.status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="active">Active Current Session</option>
                        <option value="inactive">Inactive / Upcoming</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl shadow hover:bg-blue-700 transition">Update Session</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="deleteModal = false">
            <div class="p-6 text-center space-y-4">
                <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-xl shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-base">Delete Academic Session?</h4>
                    <p class="text-xs text-slate-500 mt-1">Are you sure you want to delete session <span class="font-bold text-slate-800" x-text="deleteSession.name"></span>? This action cannot be undone.</p>
                </div>
                <form :action="'{{ url('/academics/sessions') }}/' + deleteSession.id" method="POST" class="pt-2 flex justify-center space-x-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-bold rounded-xl text-xs shadow hover:bg-rose-700 transition">Delete Session</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
