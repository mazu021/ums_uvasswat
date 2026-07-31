@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('header_title', 'Student Portal Dashboard')

@section('content')
<div class="space-y-8">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-navy-900 via-navy-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
        <div class="relative z-10">
            <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-semibold rounded-full mb-3 border border-emerald-500/30">
                Reg: {{ $student->registration_number }} | Roll: {{ $student->roll_number }}
            </span>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Welcome, {{ $student->full_name }}!</h1>
            <p class="text-slate-300 text-xs md:text-sm mt-1 max-w-xl">
                {{ $student->department->name }} • Semester {{ $student->current_semester }} • {{ $student->department->faculty->name }}
            </p>
        </div>
        <div class="mt-4 md:mt-0 relative z-10 flex space-x-3">
            <a href="{{ route('student.fees') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg transition">
                <i class="fa-solid fa-file-invoice-dollar me-1.5"></i> View Fee Challan
            </a>
        </div>
        <i class="fa-solid fa-graduation-cap absolute right-6 -bottom-6 text-slate-800 text-9xl opacity-30"></i>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: CGPA -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cumulative GPA</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $cgpa }} <span class="text-xs font-normal text-slate-400">/ 4.00</span></h3>
                @if($cgpa >= 3.66)
                    <p class="text-[11px] text-emerald-600 font-extrabold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-award text-amber-500"></i> Dean's Honor Roll
                    </p>
                @elseif($cgpa >= 3.00)
                    <p class="text-[11px] text-emerald-600 font-extrabold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> Good Standing
                    </p>
                @elseif($cgpa >= 2.00)
                    <p class="text-[11px] text-blue-600 font-bold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-info"></i> Satisfactory
                    </p>
                @elseif($cgpa > 0)
                    <p class="text-[11px] text-amber-600 font-bold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i> Academic Warning (&lt;2.0)
                    </p>
                @else
                    <p class="text-[11px] text-slate-400 font-medium mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-info"></i> Results Awaited
                    </p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>

        <!-- Card 2: Enrolled Courses -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase">Enrolled Courses</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $courses->count() }} Subjects</h3>
                <p class="text-[11px] text-slate-500 font-medium mt-1">
                    Semester {{ $student->current_semester }} Catalog
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-book-open"></i>
            </div>
        </div>

        <!-- Card 3: Attendance Percentage -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase">Attendance Record</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $attendancePercentage }}%</h3>
                @if($attendancePercentage >= 75)
                    <p class="text-[11px] text-emerald-600 font-semibold mt-1">
                        <i class="fa-solid fa-circle-check me-1"></i> Exam Eligible (≥75%)
                    </p>
                @elseif($attendancePercentage > 0)
                    <p class="text-[11px] text-amber-600 font-semibold mt-1">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Low Attendance (&lt;75%)
                    </p>
                @else
                    <p class="text-[11px] text-slate-400 font-semibold mt-1">
                        <i class="fa-solid fa-circle-info me-1"></i> No Lectures Conducted
                    </p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <!-- Card 4: Fee Challan Status -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            @php $latestChallan = $student->feeChallans->first(); @endphp
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase">Fee Status</p>
                <h3 class="text-lg font-bold text-slate-900 mt-1 uppercase">
                    {{ $latestChallan ? str_replace('_', ' ', $latestChallan->status) : 'No Challan' }}
                </h3>
                <p class="text-[11px] text-slate-500 font-medium mt-1">
                    Rs. {{ number_format($latestChallan->total_amount ?? 0, 2) }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>
        </div>
    </div>

    <!-- Enrolled Courses & Upcoming Exams -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Courses List -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-base text-slate-900 flex items-center">
                    <i class="fa-solid fa-book me-2 text-emerald-600"></i> My Enrolled Courses (Sem {{ $student->current_semester }})
                </h3>
                <a href="{{ route('student.courses') }}" class="text-xs font-bold text-emerald-600 hover:underline">Full Details</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($activeOfferings as $offering)
                    @php
                        $g = isset($offeringGrades) ? $offeringGrades->get($offering->id) : null;
                    @endphp
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded">{{ $offering->course->code ?? 'COURSE' }}</span>
                            <span class="text-[10px] font-bold text-slate-500">{{ $offering->course->credit_hours ?? 3 }} Cr.Hr</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-900 leading-tight">{{ $offering->course->title ?? '' }}</h4>
                        
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200/60 text-[11px]">
                            <span class="text-slate-500 truncate max-w-[140px]">
                                Instructor: <strong>{{ $offering->teacher->full_name ?? 'Faculty Member' }}</strong>
                            </span>

                            @if($g && $g->grade)
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded-md shrink-0">
                                    Grade {{ $g->grade }} ({{ number_format($g->gpa_point, 2) }})
                                </span>
                            @elseif($g && $g->total_marks !== null)
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 font-extrabold text-[10px] rounded-md shrink-0">
                                    {{ number_format($g->total_marks, 1) }} Marks
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-slate-200 text-slate-600 font-medium text-[10px] rounded-md shrink-0">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center col-span-2">No courses registered for current semester.</p>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Examinations Countdown -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="font-bold text-base text-slate-900 flex items-center">
                <i class="fa-solid fa-clock me-2 text-purple-600"></i> Upcoming Examinations
            </h3>
            <div class="space-y-3">
                @forelse($upcomingExams as $ex)
                    <div class="p-3 bg-purple-50 rounded-xl border-l-4 border-purple-500 space-y-1 text-xs">
                        <div class="flex justify-between font-bold text-purple-900">
                            <span>{{ $ex->title }}</span>
                            <span>{{ $ex->exam_date->format('M d') }}</span>
                        </div>
                        <p class="text-[11px] text-slate-600">{{ $ex->course->course_code }}: {{ $ex->course->title }}</p>
                        <p class="text-[10px] text-slate-400">Venue: {{ $ex->room_no ?? 'Hall A' }} | {{ $ex->start_time ?? '09:00 AM' }}</p>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-6 text-center">No upcoming scheduled exams.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
