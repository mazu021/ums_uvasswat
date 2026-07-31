@extends('layouts.app')

@section('title', 'My Exam Marks & Academic Result')
@section('header_title', 'Student Examination & Result Portal')

@section('content')
<div class="space-y-6">

    <!-- CGPA & Student Overview Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-navy-900 to-indigo-950 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center md:text-left">
            <div class="inline-flex items-center space-x-2 px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold rounded-full">
                <i class="fa-solid fa-award text-amber-400"></i>
                <span>Academic Record • Semester {{ $student->current_semester ?? 1 }}</span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight">{{ $student->full_name }}</h1>
            <p class="text-xs text-slate-300">
                Reg No: <span class="font-mono font-bold text-amber-300">{{ $student->registration_number ?? 'N/A' }}</span> 
                • {{ $student->program->name ?? ($student->department->name ?? 'Degree Program') }}
            </p>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Cumulative CGPA Card -->
            <div class="bg-white/10 backdrop-blur-md px-6 py-3 rounded-2xl border border-white/15 text-center">
                <p class="text-[10px] uppercase font-bold text-slate-300 tracking-wider">Cumulative GPA</p>
                <h2 class="text-3xl font-extrabold text-amber-400 mt-0.5">{{ $cgpa }} <span class="text-xs font-normal text-slate-300">/ 4.00</span></h2>
            </div>

            <!-- Export Download Button -->
            <a href="{{ route('student.exams.export') }}" 
               class="px-5 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-extrabold text-xs rounded-xl shadow-lg transition flex items-center space-x-2">
                <i class="fa-solid fa-file-excel text-base"></i>
                <span>Download Result Sheet</span>
            </a>
        </div>
    </div>

    <!-- Course Grades Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden space-y-4">
        
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
                    Semester {{ $student->current_semester ?? 1 }} Examination Breakdown
                </h3>
                <p class="text-xs text-slate-500">Official course evaluation results: Mid Exam (30%) + Internal (20%) + Final Exam (50%) = Total Marks (100%)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-4 py-3.5 w-12 text-center">S.No</th>
                        <th class="px-4 py-3.5">Course Details</th>
                        <th class="px-4 py-3.5 text-center">Credit Hours</th>
                        <th class="px-4 py-3.5 text-center bg-blue-50/50 text-blue-900">Mid Exam (30)</th>
                        <th class="px-4 py-3.5 text-center bg-amber-50/50 text-amber-900">Internal (20)</th>
                        <th class="px-4 py-3.5 text-center bg-purple-50/50 text-purple-900">Final Exam (50)</th>
                        <th class="px-4 py-3.5 text-center font-extrabold text-slate-900">Total (100)</th>
                        <th class="px-4 py-3.5 text-center">Grade</th>
                        <th class="px-4 py-3.5 text-center">GPA Point</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($activeOfferings as $index => $offering)
                        @php 
                            $g = $offeringGrades->get($offering->id);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-4">
                                <div class="font-extrabold text-slate-900 text-sm">
                                    {{ $offering->course->code ?? 'COURSE' }} - {{ $offering->course->title ?? 'Course Title' }}
                                </div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span><i class="fa-solid fa-user-tie text-emerald-600 me-1"></i> Instructor: {{ $offering->teacher->full_name ?? 'Faculty Member' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center font-bold text-slate-700">
                                {{ $offering->course->credit_hours ?? 3 }} Cr.Hr
                            </td>

                            <!-- Mid Exam (30%) -->
                            <td class="px-4 py-4 text-center font-bold text-blue-900 bg-blue-50/30">
                                {{ $g && $g->mid_marks !== null ? number_format($g->mid_marks, 1) : '-' }}
                            </td>

                            <!-- Internal (20%) -->
                            <td class="px-4 py-4 text-center font-bold text-amber-900 bg-amber-50/30">
                                {{ $g && $g->internal_marks !== null ? number_format($g->internal_marks, 1) : '-' }}
                            </td>

                            <!-- Final Exam (50%) -->
                            <td class="px-4 py-4 text-center font-bold text-purple-900 bg-purple-50/30">
                                {{ $g && $g->final_marks !== null ? number_format($g->final_marks, 1) : '-' }}
                            </td>

                            <!-- Total Marks -->
                            <td class="px-4 py-4 text-center font-extrabold text-slate-900 text-sm">
                                {{ $g && $g->total_marks !== null ? number_format($g->total_marks, 1) : '-' }}
                            </td>

                            <!-- Letter Grade -->
                            <td class="px-4 py-4 text-center">
                                @if($g && $g->grade)
                                    <span class="px-3 py-1 font-extrabold rounded-lg text-xs {{ $g->grade == 'F' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $g->grade }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-400 font-semibold text-[11px] rounded">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <!-- GPA Point -->
                            <td class="px-4 py-4 text-center font-bold text-slate-800">
                                {{ $g && $g->gpa_point !== null ? number_format($g->gpa_point, 2) : '0.00' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400 font-medium">
                                <i class="fa-solid fa-folder-open text-2xl block mb-2 opacity-40"></i>
                                No active course offerings registered for current semester.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
