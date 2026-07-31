@extends('layouts.app')

@section('title', 'Attendance History')
@section('header_title', 'Attendance Session Logs')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-extrabold text-xs rounded-lg">
                    {{ $courseOffering->course->course_code }}
                </span>
                <span class="text-xs font-semibold text-slate-500">
                    {{ $courseOffering->program->name }} (Sem {{ $courseOffering->semester_number }})
                </span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-2">{{ $courseOffering->course->title }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Teacher: <strong>{{ $courseOffering->teacher->name }}</strong> | Batch: <strong>{{ $courseOffering->batch->name }}</strong>
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('attendance.mark.form', $courseOffering->id) }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition">
                <i class="fa-solid fa-plus me-1"></i> New Attendance
            </a>
            <a href="{{ route('attendance.teacher.dashboard') }}" class="px-3 py-2.5 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                Dashboard
            </a>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Lecture #</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Topic Covered</th>
                        <th class="py-3.5 px-4 text-center">Present / Total</th>
                        <th class="py-3.5 px-4 text-center">Percentage</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($sessions as $sess)
                    @php
                        $perc = $sess->total_students > 0 ? round(($sess->present_count / $sess->total_students) * 100, 1) : 0;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-900">
                            Lecture #{{ $sess->lecture_number }}
                        </td>
                        <td class="py-3.5 px-4 font-medium">
                            {{ $sess->attendance_date->format('M d, Y') }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            {{ $sess->topic ?? 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-semibold">
                            <span class="text-emerald-600">{{ $sess->present_count }}</span> / {{ $sess->total_students }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $perc >= 75 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $perc }}%
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('attendance.session.detail', $sess->id) }}" class="px-3 py-1 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition">
                                View Sheet
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            No attendance sessions recorded yet for this course offering.
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
