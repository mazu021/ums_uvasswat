@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Admissions Portal</h1>
            <p class="text-sm text-slate-500">Manage undergraduate & postgraduate admission applications, merit lists, and enrollments for UVAS Swat.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admissions.apply') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium text-sm transition shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>New Application</span>
            </a>
            <a href="{{ route('admissions.merit-list') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm transition shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-list-ol"></i>
                <span>View Merit List</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('admissions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Statuses</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="fee_pending" {{ request('status') == 'fee_pending' ? 'selected' : '' }}>Fee Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Program</label>
                <select name="program_id" onchange="this.form.submit()" class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->name }} ({{ $prog->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, App #, CNIC..." class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-medium transition">Filter</button>
                <a href="{{ route('admissions.index') }}" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-medium transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Admission Applications Directory</h2>
            <span class="text-xs bg-slate-100 text-slate-600 font-semibold px-3 py-1 rounded-full">{{ $applications->total() }} Applications</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-3.5">App #</th>
                        <th class="px-6 py-3.5">Applicant Details</th>
                        <th class="px-6 py-3.5">Program & Campus</th>
                        <th class="px-6 py-3.5 text-center">Merit Score</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                    @forelse($applications as $app)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-emerald-700">{{ $app->application_no }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900">{{ $app->applicant_name }}</div>
                            <div class="text-xs text-slate-500">S/D/O: {{ $app->father_name }} | CNIC: {{ $app->cnic }}</div>
                            <div class="text-xs text-slate-400">{{ $app->email }} | {{ $app->phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-800">{{ $app->program->name ?? 'N/A' }}</span>
                            <div class="text-xs text-slate-500">{{ $app->campus->name ?? 'Main Campus' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-sm">
                                {{ number_format($app->merit_score, 2) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $badge = match($app->status) {
                                    'submitted' => 'bg-amber-100 text-amber-800',
                                    'under_review' => 'bg-blue-100 text-blue-800',
                                    'fee_pending' => 'bg-purple-100 text-purple-800',
                                    'approved' => 'bg-indigo-100 text-indigo-800',
                                    'enrolled' => 'bg-emerald-100 text-emerald-800',
                                    'rejected' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-slate-100 text-slate-800'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                                {{ strtoupper(str_replace('_', ' ', $app->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admissions.update-status', $app) }}" class="inline-flex items-center space-x-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs rounded-lg border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="submitted" {{ $app->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="under_review" {{ $app->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="approved" {{ $app->status == 'approved' ? 'selected' : '' }}>Approve</option>
                                    <option value="enrolled" {{ $app->status == 'enrolled' ? 'selected' : '' }}>Enroll Student</option>
                                    <option value="rejected" {{ $app->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">No admission applications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection
