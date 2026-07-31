@extends('layouts.app')

@section('title', 'Institutional Attendance Reports')
@section('header_title', 'Institutional Reports')

@section('content')
<div class="space-y-6">

    <!-- Title Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Master Attendance Reports</h1>
            <p class="text-xs text-slate-500 mt-1">
                Filter institutional attendance logs by department, program, course, teacher, batch, session, and date range.
            </p>
        </div>
        <div>
            <button onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-md transition">
                <i class="fa-solid fa-print me-1.5"></i> Print / Export Report
            </button>
        </div>
    </div>

    <!-- Multi-Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('attendance.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Department</label>
                <select name="department_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Program</label>
                <select name="program_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                            {{ $prog->code }} - {{ $prog->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Teacher</label>
                <select name="teacher_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Course</label>
                <select name="course_id" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->course_code }} - {{ $c->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Academic Session</label>
                <select name="academic_session_id" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
                    <option value="">All Sessions</option>
                    @foreach($academicSessions as $as)
                        <option value="{{ $as->id }}" {{ request('academic_session_id') == $as->id ? 'selected' : '' }}>
                            {{ $as->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition">
                    Apply Filters
                </button>
                <a href="{{ route('attendance.reports.index') }}" class="px-3 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Reports Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Session Date & Lec</th>
                        <th class="py-3.5 px-4">Course & Program</th>
                        <th class="py-3.5 px-4">Assigned Teacher</th>
                        <th class="py-3.5 px-4">Topic Covered</th>
                        <th class="py-3.5 px-4 text-center">Present / Total</th>
                        <th class="py-3.5 px-4 text-center">Attendance %</th>
                        <th class="py-3.5 px-4 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($sessions as $sess)
                    @php
                        $perc = $sess->total_students > 0 ? round(($sess->present_students / $sess->total_students) * 100, 1) : 0;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900">{{ $sess->attendance_date->format('M d, Y') }}</div>
                            <div class="text-[10px] text-slate-400">Lecture #{{ $sess->lecture_number }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-emerald-700">{{ $sess->courseOffering->course->course_code ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-500">{{ $sess->courseOffering->program->code ?? '' }} (Sem {{ $sess->courseOffering->semester_number }})</div>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                            {{ $sess->courseOffering->teacher->name ?? 'Faculty' }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            {{ $sess->topic ?? 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold">
                            <span class="text-emerald-600">{{ $sess->present_students }}</span> / {{ $sess->total_students }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $perc >= 75 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $perc }}%
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('attendance.session.detail', $sess->id) }}" class="px-2.5 py-1 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition">
                                View Sheet
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">
                            No attendance records match the selected filter criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sessions->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
