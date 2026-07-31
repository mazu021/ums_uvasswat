@extends('layouts.app')

@section('title', 'Sections Management')
@section('header_title', 'Class Sections & Shift Management')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Class Sections</h3>
            <p class="text-xs text-slate-500">Manage student class sections (Section A, Section B, Morning & Evening shifts).</p>
        </div>
        <button @click="createModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
            <i class="fa-solid fa-layer-group"></i>
            <span>Add Class Section</span>
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Section Name</th>
                        <th class="px-6 py-4">Program</th>
                        <th class="px-6 py-4">Batch</th>
                        <th class="px-6 py-4">Capacity</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sections as $sec)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-bold text-slate-900 flex items-center space-x-2">
                                <span class="w-7 h-7 bg-indigo-100 text-indigo-900 rounded-lg flex items-center justify-center font-extrabold text-xs">
                                    {{ substr($sec->name, 0, 2) }}
                                </span>
                                <span>{{ $sec->name }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $sec->program->name ?? 'All Programs' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $sec->batch->name ?? 'All Batches' }}
                            </td>
                            <td class="px-6 py-4 font-bold font-mono text-emerald-700">
                                {{ $sec->capacity ?? 50 }} Students
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $sec->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $sec->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs">No sections configured.</td>
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
                <h4 class="font-bold text-sm">Create Class Section</h4>
                <button @click="createModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('academics.sections.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Section Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Section A (Morning)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Program</label>
                        <select name="program_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                            <option value="">All Programs</option>
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Max Capacity</label>
                        <input type="number" name="capacity" value="50" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status *</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl shadow">Save Section</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
