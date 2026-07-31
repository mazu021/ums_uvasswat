@extends('layouts.app')

@section('title', 'My Attendance')
@section('header_title', 'Student Attendance Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Student Info Header -->
    <div class="bg-gradient-to-r from-navy-900 via-indigo-950 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-semibold rounded-full mb-3 border border-emerald-500/30">
                Official Academic Attendance Record
            </span>
            <h1 class="text-2xl font-bold">{{ $student->full_name }}</h1>
            <p class="text-slate-300 text-xs mt-1">
                Roll No: <strong>{{ $student->roll_number }}</strong> | Reg No: <strong>{{ $student->registration_number }}</strong>
            </p>
            <p class="text-slate-400 text-xs mt-0.5">
                {{ $student->department->name ?? ($student->program->name ?? 'Academic Department') }} — Semester {{ $student->current_semester }}
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/10 text-center">
            <p class="text-xs text-slate-300 font-semibold uppercase tracking-wider">Overall Attendance</p>
            <div class="text-3xl font-extrabold text-white mt-1">{{ $overallPercentage }}%</div>
            <p class="text-[10px] text-emerald-400 font-bold mt-0.5">
                {{ $totalSessionCount }} Total Conducted Lectures
            </p>
        </div>
    </div>

    <!-- Subject-Wise Attendance Cards -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($subjectStats as $stat)
        @php
            $offering = $stat['offering'];
            $perc = $stat['percentage'];
            $records = $stat['records'];
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4" x-data="{ showDetails: false }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 font-extrabold text-xs rounded-lg">
                            {{ $offering->course->course_code }}
                        </span>
                        <span class="text-xs font-bold text-slate-500">Sem {{ $offering->semester_number }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 leading-snug">
                        {{ $offering->course->title }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Instructor: <span class="font-semibold text-slate-700">{{ $offering->teacher->name ?? 'Faculty Member' }}</span>
                    </p>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-xl font-extrabold {{ $perc >= 75 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $perc }}%
                        </div>
                        <div class="text-[10px] font-bold text-slate-400">Attendance Score</div>
                    </div>
                    <button @click="showDetails = !showDetails" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center space-x-1.5">
                        <i class="fa-solid" :class="showDetails ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        <span x-text="showDetails ? 'Hide History' : 'View History'"></span>
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div>
                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden mb-2">
                    <div class="h-full rounded-full transition-all duration-500 {{ $perc >= 75 ? 'bg-emerald-500' : ($perc >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                         style="width: {{ $perc }}%"></div>
                </div>

                <div class="grid grid-cols-3 gap-3 text-center text-xs bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <div>
                        <div class="text-slate-400 font-semibold text-[10px]">Conducted Lectures</div>
                        <div class="font-extrabold text-slate-800 text-sm mt-0.5">{{ $stat['total_lectures'] }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 font-semibold text-[10px]">Lectures Attended</div>
                        <div class="font-extrabold text-emerald-600 text-sm mt-0.5">{{ $stat['present_count'] }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 font-semibold text-[10px]">Absences</div>
                        <div class="font-extrabold text-red-600 text-sm mt-0.5">{{ $stat['absent_count'] }}</div>
                    </div>
                </div>
            </div>

            @if($perc < 75)
            <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-[11px] text-red-800 font-medium flex items-center space-x-2">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
                <span><strong>Warning:</strong> Attendance is below 75% threshold required for exam qualification.</span>
            </div>
            @endif

            <!-- Lecture-by-Lecture History Breakdown -->
            <div x-show="showDetails" x-transition class="pt-3 border-t border-slate-100 space-y-3">
                <h4 class="font-bold text-xs text-slate-800 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left me-1.5 text-indigo-600"></i>
                    Lecture-by-Lecture Attendance Log
                </h4>

                @if($records->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-xs text-left text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b text-[10px]">
                            <tr>
                                <th class="px-4 py-2.5">Date</th>
                                <th class="px-4 py-2.5">Lecture #</th>
                                <th class="px-4 py-2.5">Topic Covered</th>
                                <th class="px-4 py-2.5">Status</th>
                                <th class="px-4 py-2.5">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($records as $rec)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 font-bold text-slate-800">{{ $rec->session->attendance_date ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 font-bold text-indigo-700">Lecture {{ $rec->session->lecture_number ?? 1 }}</td>
                                <td class="px-4 py-2.5 text-slate-700">{{ $rec->session->topic ?? 'Course Lecture' }}</td>
                                <td class="px-4 py-2.5 font-bold">
                                    @if($rec->status === 'Present')
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md text-[10px]">Present</span>
                                    @elseif($rec->status === 'Late')
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-md text-[10px]">Late</span>
                                    @elseif($rec->status === 'Leave')
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md text-[10px]">Leave</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-md text-[10px]">Absent</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-slate-500">{{ $rec->remarks ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-xs text-slate-400 italic bg-slate-50 p-3 rounded-xl text-center">
                    No lecture attendance has been submitted by the instructor for this course yet.
                </p>
                @endif
            </div>

        </div>
        @empty
        <div class="bg-white p-12 text-center rounded-2xl border border-slate-200 text-slate-400 space-y-2">
            <i class="fa-solid fa-clipboard-user text-4xl text-slate-300"></i>
            <p class="font-bold text-slate-600 text-sm">No active course offerings found for your department and semester.</p>
            <p class="text-xs text-slate-400">Please ensure your department and current semester match your assigned courses.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
