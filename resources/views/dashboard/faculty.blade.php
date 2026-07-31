@extends('layouts.app')

@section('title', 'Faculty Portal')
@section('header_title', 'Faculty Portal')

@section('content')
<div class="space-y-8">

    <!-- Faculty Banner -->
    <div class="bg-gradient-to-r from-navy-900 via-navy-800 to-indigo-950 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
        <div class="relative z-10">
            <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-semibold rounded-full mb-3 border border-emerald-500/30">
                Active Role: Faculty / Academic Instructor
            </span>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Welcome back, {{ $user->name }}!</h1>
            <p class="text-slate-300 text-xs mt-1 max-w-xl">
                University of Veterinary and Animal Sciences, Swat (UVAS Swat) Faculty Portal.
            </p>
        </div>
        <div class="mt-4 md:mt-0 relative z-10 flex space-x-3">
            <a href="{{ route('hr.leaves.index') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg transition">
                <i class="fa-solid fa-umbrella-beach me-1.5"></i> Apply for Leave
            </a>
        </div>
        <i class="fa-solid fa-graduation-cap absolute right-6 -bottom-6 text-slate-800 text-9xl opacity-30"></i>
    </div>

    <!-- Faculty Dashboard Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Assigned Offerings -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Assigned Courses</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $myOfferings->count() }}</h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1">Active Offerings</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-book-open"></i>
            </div>
        </div>

        <!-- Total Enrolled Students across assigned offerings -->
        @php
            $totalAssignedStudents = 0;
            foreach($myOfferings as $off) {
                $totalAssignedStudents += $off->getEnrolledStudents()->count();
            }
        @endphp
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Assigned Students</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalAssignedStudents) }}</h3>
                <p class="text-[11px] text-blue-600 font-semibold mt-1">Across {{ $myOfferings->count() }} Classes</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Attendance Sessions Taken -->
        @php
            $totalSessionsTaken = 0;
            foreach($myOfferings as $off) {
                $totalSessionsTaken += $off->attendanceSessions()->count();
            }
        @endphp
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lectures Taken</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalSessionsTaken }}</h3>
                <p class="text-[11px] text-purple-600 font-semibold mt-1">Conduct & Marked</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>

        <!-- My Leaves -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Leave Applications</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $myLeaves->count() }}</h3>
                <p class="text-[11px] text-amber-600 font-semibold mt-1">My Leave Status</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-umbrella-beach"></i>
            </div>
        </div>
    </div>

    <!-- Assigned Courses & Classes Table (With Take Attendance & Download PDF/Excel) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">My Assigned Courses & Classes</h2>
                <p class="text-xs text-slate-500">Take lecture attendance, manage marks, or download student lists for assigned courses.</p>
            </div>
            <a href="{{ route('attendance.teacher.dashboard') }}" class="px-3.5 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-200 transition">
                View All Hub
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($myOfferings as $offering)
            @php
                $enrolledCount = $offering->getEnrolledStudents()->count();
                $sessionsCount = $offering->attendanceSessions()->count();
            @endphp
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-extrabold text-xs rounded-lg">
                            {{ $offering->course->course_code }}
                        </span>
                        <span class="text-xs font-semibold text-slate-600">
                            Sem {{ $offering->semester_number }} 
                            @if($offering->section)<span class="px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded font-bold ms-1">{{ $offering->section->name }}</span>@endif
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 leading-tight mb-2">
                        {{ $offering->course->title }}
                    </h3>

                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 my-3 bg-white p-3 rounded-xl border border-slate-200">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Department:</span>
                            <span class="font-bold text-slate-800">{{ $offering->program->department->name ?? ($offering->course->department->name ?? 'General Academic') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Degree Program:</span>
                            <span class="font-semibold text-slate-700">{{ $offering->program->name ?? 'Degree Program' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Enrolled Students:</span>
                            <span class="font-bold text-emerald-600">{{ $enrolledCount }} Students</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Sessions Taken:</span>
                            <span class="font-bold text-purple-600">{{ $sessionsCount }} Lectures</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Take Attendance, Marks, Download PDF/Excel -->
                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-200">
                    <a href="{{ route('attendance.mark.form', $offering->id) }}" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-xs text-center transition">
                        <i class="fa-solid fa-clipboard-user me-1"></i> Take Attendance
                    </a>
                    <a href="{{ route('academics.exams.index', ['course_offering_id' => $offering->id]) }}" class="px-3 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-xs text-center transition" title="Enter Course Exam Marks & Gradebook">
                        <i class="fa-solid fa-award me-1"></i> Marks & Gradebook
                    </a>
                    <a href="{{ route('course-offerings.export-students', $offering->id) }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition" title="Download Student List (Excel/CSV)">
                        <i class="fa-solid fa-file-excel me-1"></i> Download List
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-8 text-center text-slate-400">
                No active courses assigned to your faculty account yet.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Secondary Row: Quick Actions & My Leaves -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Faculty Quick Actions -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h3 class="font-bold text-base text-slate-900">Faculty Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('attendance.teacher.dashboard') }}" class="flex items-center justify-between p-3 bg-slate-50 hover:bg-emerald-50 rounded-xl transition border border-slate-200">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-layer-group text-emerald-600"></i>
                        <span class="text-xs font-bold text-slate-800">Assigned Courses & Classes</span>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-400 text-xs"></i>
                </a>
                <a href="{{ route('academics.exams.index') }}" class="flex items-center justify-between p-3 bg-slate-50 hover:bg-blue-50 rounded-xl transition border border-slate-200">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-pen-ruler text-blue-600"></i>
                        <span class="text-xs font-bold text-slate-800">Upload Exam Marks & Grades</span>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-400 text-xs"></i>
                </a>
                <a href="{{ route('hr.leaves.index') }}" class="flex items-center justify-between p-3 bg-slate-50 hover:bg-amber-50 rounded-xl transition border border-slate-200">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-umbrella-beach text-amber-600"></i>
                        <span class="text-xs font-bold text-slate-800">Apply for Leave</span>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-400 text-xs"></i>
                </a>
                <a href="{{ route('attendance.reports.index') }}" class="flex items-center justify-between p-3 bg-slate-50 hover:bg-purple-50 rounded-xl transition border border-slate-200">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-chart-pie text-purple-600"></i>
                        <span class="text-xs font-bold text-slate-800">My Attendance Reports</span>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-400 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- My Leaves Status Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-base text-slate-900">My Leave Applications</h3>
                    <p class="text-xs text-slate-500">Track application status for requested leaves.</p>
                </div>
                <a href="{{ route('hr.leaves.index') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition">
                    + Apply Leave
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-2.5 px-3">Type</th>
                            <th class="py-2.5 px-3">Dates</th>
                            <th class="py-2.5 px-3">Days</th>
                            <th class="py-2.5 px-3">Reason</th>
                            <th class="py-2.5 px-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($myLeaves as $leave)
                        <tr>
                            <td class="py-2.5 px-3 font-bold text-slate-900">{{ $leave->leaveType->name ?? 'Leave' }}</td>
                            <td class="py-2.5 px-3">{{ $leave->start_date }} to {{ $leave->end_date }}</td>
                            <td class="py-2.5 px-3 font-semibold">{{ $leave->total_days }} days</td>
                            <td class="py-2.5 px-3 text-slate-500 max-w-xs truncate">{{ $leave->reason }}</td>
                            <td class="py-2.5 px-3 text-right">
                                @if($leave->status === 'approved')
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold text-[10px] rounded-full">Approved</span>
                                @elseif($leave->status === 'rejected')
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 font-bold text-[10px] rounded-full">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 font-bold text-[10px] rounded-full">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">No leave applications submitted yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
