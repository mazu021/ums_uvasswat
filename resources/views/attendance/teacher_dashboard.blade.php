@extends('layouts.app')

@section('title', 'Teacher Attendance Hub')
@section('header_title', 'Teacher Academic Hub')

@section('content')
<div class="space-y-6">

    <!-- Teacher Banner -->
    <div class="bg-gradient-to-r from-emerald-800 via-teal-900 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
        <div class="relative z-10">
            <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-semibold rounded-full mb-3 border border-emerald-500/30">
                Teacher Attendance Portal
            </span>
            <h1 class="text-2xl font-bold">My Course Offerings</h1>
            <p class="text-slate-300 text-xs mt-1 max-w-xl">
                Select a course offering below to take lecture attendance or view historical attendance sheets.
            </p>
        </div>
        <div class="mt-4 md:mt-0 relative z-10">
            <a href="{{ route('attendance.reports.index') }}" class="px-4 py-2.5 bg-white text-emerald-900 font-bold text-xs rounded-xl shadow-lg hover:bg-slate-100 transition">
                <i class="fa-solid fa-chart-pie me-1"></i> Attendance Reports
            </a>
        </div>
    </div>

    <!-- Assigned Offerings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($offerings as $offering)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-extrabold text-xs rounded-lg border border-emerald-200/60">
                        {{ $offering->course->course_code ?? 'N/A' }}
                    </span>
                    <span class="text-[11px] font-semibold text-slate-500">
                        Semester {{ $offering->semester_number }}
                        @if($offering->section)
                            <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded-md font-bold ms-1">{{ $offering->section->name }}</span>
                        @endif
                    </span>
                </div>

                <h3 class="text-base font-bold text-slate-900 leading-tight mb-2">
                    {{ $offering->course->title ?? 'Untitled Course' }}
                </h3>

                <div class="space-y-1.5 text-xs text-slate-600 mt-4 border-t border-slate-100 pt-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Program:</span>
                        <span class="font-semibold text-slate-800">{{ $offering->program->name ?? '' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Batch:</span>
                        <span class="font-medium text-slate-700">{{ $offering->batch->name ?? '' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Academic Session:</span>
                        <span class="font-medium text-slate-700">{{ $offering->academicSession->name ?? '' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Lectures Taken:</span>
                        <span class="font-bold text-emerald-600">{{ $offering->attendanceSessions->count() }} Sessions</span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center space-x-2">
                <a href="{{ route('attendance.mark.form', $offering->id) }}" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-xs text-center transition">
                    <i class="fa-solid fa-square-check me-1.5"></i> Take Attendance
                </a>
                <a href="{{ route('attendance.offering.history', $offering->id) }}" class="p-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl transition" title="View History">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200">
            <i class="fa-solid fa-graduation-cap text-4xl text-slate-300 mb-3"></i>
            <h4 class="text-base font-bold text-slate-700">No Course Offerings Assigned</h4>
            <p class="text-xs text-slate-400 mt-1">You currently have no active course offerings assigned for this academic session.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
