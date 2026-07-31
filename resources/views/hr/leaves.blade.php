@extends('layouts.app')

@section('title', 'Leave Management')
@section('header_title', 'Employee Leave Applications & Past Records Registry')

@section('content')
<div class="space-y-6" x-data="{ applyModal: false }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Faculty & Staff Leave Registry</h3>
            <p class="text-xs text-slate-500">Track complete past and active leave records across all university departments, faculty, and administrative staff.</p>
        </div>
        <button @click="applyModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
            <i class="fa-solid fa-plane-departure"></i>
            <span>Apply For Leave</span>
        </button>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Leave Records</p>
                <p class="text-lg font-extrabold text-slate-800">{{ number_format($stats['total']) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Approved Leaves</p>
                <p class="text-lg font-extrabold text-emerald-700">{{ number_format($stats['approved']) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pending Approvals</p>
                <p class="text-lg font-extrabold text-amber-700">{{ number_format($stats['pending']) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rejected Requests</p>
                <p class="text-lg font-extrabold text-rose-700">{{ number_format($stats['rejected']) }}</p>
            </div>
        </div>
    </div>

    <!-- Search & Filters Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('hr.leaves.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Search Staff / Reason</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Code, Designation..." class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Leave Type</label>
                <select name="leave_type_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="all">All Leave Types</option>
                    @foreach($leaveTypes as $lt)
                        <option value="{{ $lt->id }}" {{ request('leave_type_id') == $lt->id ? 'selected' : '' }}>{{ $lt->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Year</label>
                <select name="year" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="all">All Years</option>
                    <option value="2026" {{ request('year') === '2026' ? 'selected' : '' }}>2026</option>
                    <option value="2025" {{ request('year') === '2025' ? 'selected' : '' }}>2025</option>
                    <option value="2024" {{ request('year') === '2024' ? 'selected' : '' }}>2024</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow text-xs transition flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('hr.leaves.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition flex items-center justify-center">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Leave Applications Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Applicant & Role</th>
                        <th class="px-6 py-4">Leave Type</th>
                        <th class="px-6 py-4">Start Date</th>
                        <th class="px-6 py-4">End Date</th>
                        <th class="px-6 py-4">Duration</th>
                        <th class="px-6 py-4">Reason</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Approval Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($app->employee->first_name ?? 'E', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 leading-tight">
                                            {{ $app->employee->full_name ?? ($app->employee->first_name . ' ' . $app->employee->last_name) }}
                                        </p>
                                        <p class="text-[10px] text-slate-500 font-medium">
                                            {{ $app->employee->designation ?? 'Staff Member' }} 
                                            @if($app->employee->employee_code)
                                                <span class="text-slate-400">({{ $app->employee->employee_code }})</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-umbrella-beach text-[9px]"></i>
                                    <span>{{ $app->leaveType->name }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-700">
                                {{ $app->start_date ? $app->start_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-700">
                                {{ $app->end_date ? $app->end_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md text-[11px]">
                                    {{ $app->total_days }} {{ Str::plural('Day', $app->total_days) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 max-w-xs truncate" title="{{ $app->reason }}">
                                {{ $app->reason }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $app->status === 'approved' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($app->status === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-300') }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($app->status === 'pending')
                                    <form action="{{ route('hr.leaves.update-status', $app->id) }}" method="POST" class="inline-flex space-x-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="status" value="approved" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition">Approve</button>
                                        <button type="submit" name="status" value="rejected" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-[10px] transition">Reject</button>
                                    </form>
                                @else
                                    <span class="text-[10px] font-semibold text-slate-400">
                                        {{ $app->approver ? 'By ' . $app->approver->name : 'Processed' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400 text-xs">
                                <i class="fa-regular fa-folder-open text-2xl mb-2 text-slate-300 block"></i>
                                <span>No leave applications or past records match your criteria.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

    <!-- Apply Leave Modal -->
    <div x-show="applyModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="applyModal = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-plane-departure text-emerald-400"></i>
                    <h4 class="font-bold text-sm">Submit Leave Application</h4>
                </div>
                <button @click="applyModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('hr.leaves.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Leave Type *</label>
                    <select name="leave_type_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($leaveTypes as $lt)
                            <option value="{{ $lt->id }}">{{ $lt->name }} (Allowed: {{ $lt->days_allowed }} days)</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Start Date *</label>
                        <input type="date" name="start_date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">End Date *</label>
                        <input type="date" name="end_date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Reason for Leave *</label>
                    <textarea name="reason" rows="3" required placeholder="Provide detailed justification..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                </div>
                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="applyModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl shadow hover:bg-emerald-700 transition">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
