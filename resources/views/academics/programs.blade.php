@extends('layouts.app')

@section('title', 'Degree Programs Management')
@section('header_title', 'Degree Programs & Academic Offerings')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Degree Programs</h3>
            <p class="text-xs text-slate-500">Official university degree programs across DVM, Allied Health, Sciences, Arts, and Diplomas.</p>
        </div>
        <button @click="createModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>Add New Degree Program</span>
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Program Code</th>
                        <th class="px-6 py-4">Program Name</th>
                        <th class="px-6 py-4">Department & Faculty</th>
                        <th class="px-6 py-4">Duration / Semesters</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($programs as $prog)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-bold text-indigo-900">
                                <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-200 rounded-lg text-xs">
                                    {{ $prog->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $prog->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 block">{{ $prog->department->name ?? 'N/A' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $prog->department->faculty->name ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700">
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-900 border border-amber-200 rounded-lg text-xs font-mono">
                                    {{ $prog->total_semesters }} Semesters
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $prog->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $prog->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs">No programs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="createModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="createModal = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Create New Degree Program</h4>
                <button @click="createModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('academics.programs.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Program Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Doctor of Veterinary Medicine (DVM) Open Merit" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Program Code *</label>
                        <input type="text" name="code" required placeholder="e.g. DVM-OPEN" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold uppercase">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Total Semesters *</label>
                        <input type="number" name="total_semesters" required min="1" max="12" value="8" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Department *</label>
                    <select name="department_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Degree Level *</label>
                        <select name="degree_level" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                            <option value="Undergraduate">Undergraduate (BS/DVM/DPT)</option>
                            <option value="Diploma">Diploma (DVS/ADCP)</option>
                            <option value="Postgraduate">Postgraduate</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status *</label>
                        <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl shadow">Save Program</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
