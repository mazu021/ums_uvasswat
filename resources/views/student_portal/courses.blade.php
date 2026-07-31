@extends('layouts.app')

@section('title', 'My Courses Repository & History')
@section('header_title', 'Student Enrolled & Past Courses Repository')

@section('content')
<div class="space-y-6" x-data="{ courseTab: 'repository', selectedSem: 'all' }">

    <!-- Top Summary Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 font-extrabold text-xs rounded-full border border-emerald-500/30">
                    <i class="fa-solid fa-graduation-cap me-1"></i> Current Semester {{ $student->current_semester }}
                </span>
                <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 font-extrabold text-xs rounded-full border border-indigo-500/30">
                    {{ $student->program->name ?? ($student->department->name ?? 'Degree Program') }}
                </span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">{{ $student->full_name }}</h2>
            <p class="text-xs text-slate-300">Reg No: <span class="font-mono text-emerald-400 font-bold">{{ $student->registration_number }}</span> • Roll No: <span class="font-mono text-amber-400 font-bold">{{ $student->roll_number }}</span></p>
        </div>

        <!-- Academic Performance Stats -->
        <div class="grid grid-cols-3 gap-3 w-full md:w-auto">
            <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 text-center">
                <span class="block text-[10px] uppercase font-bold text-slate-300">Passed Courses</span>
                <span class="text-xl font-black text-emerald-400">{{ $totalPassedCourses }}</span>
            </div>
            <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 text-center">
                <span class="block text-[10px] uppercase font-bold text-slate-300">Earned Credits</span>
                <span class="text-xl font-black text-amber-400">{{ $totalCompletedCredits }} <span class="text-[10px] font-normal">Hrs</span></span>
            </div>
            <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 text-center">
                <span class="block text-[10px] uppercase font-bold text-slate-300">CGPA</span>
                <span class="text-xl font-black text-white">{{ $cgpa }}</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs & Semester Selector -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-2 w-full sm:w-auto">
            <button @click="courseTab = 'repository'" :class="courseTab === 'repository' ? 'bg-indigo-600 text-white font-extrabold shadow-md' : 'bg-slate-100 text-slate-700 font-bold hover:bg-slate-200'" class="px-4 py-2 text-xs rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-box-archive"></i>
                <span>Past & Present Courses Repository</span>
            </button>
            <button @click="courseTab = 'active'" :class="courseTab === 'active' ? 'bg-emerald-600 text-white font-extrabold shadow-md' : 'bg-slate-100 text-slate-700 font-bold hover:bg-slate-200'" class="px-4 py-2 text-xs rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-book-open"></i>
                <span>Active Semester {{ $student->current_semester }} Courses</span>
            </button>
        </div>

        <!-- Semester Filter Dropdown for Repository -->
        <div x-show="courseTab === 'repository'" class="flex items-center space-x-2 w-full sm:w-auto">
            <label class="text-xs font-bold text-slate-600 uppercase whitespace-nowrap">Filter Semester:</label>
            <select x-model="selectedSem" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500">
                <option value="all">All Semesters (Sem 1 to {{ $totalProgramSemesters }})</option>
                @for($s = 1; $s <= $totalProgramSemesters; $s++)
                    <option value="{{ $s }}">Semester {{ $s }} {{ $s == $student->current_semester ? '(Active Current)' : ($s < $student->current_semester ? '(Completed Past)' : '') }}</option>
                @endfor
            </select>
        </div>
    </div>

    <!-- TAB 1: Complete Past & Present Courses Repository -->
    <div x-show="courseTab === 'repository'" class="space-y-6">
        @for($sem = 1; $sem <= $totalProgramSemesters; $sem++)
            @php
                $semData = $semesterRepository[$sem] ?? null;
                $grades = $semData['grades'] ?? collect();
            @endphp

            @if($semData)
                <div x-show="selectedSem === 'all' || selectedSem == '{{ $sem }}'" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden space-y-0">
                    
                    <!-- Semester Card Header -->
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <span class="w-9 h-9 rounded-2xl flex items-center justify-center font-black text-sm {{ $sem == $student->current_semester ? 'bg-emerald-600 text-white shadow-md' : ($sem < $student->current_semester ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-600') }}">
                                {{ $sem }}
                            </span>
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-sm">
                                    Semester {{ $sem }}
                                    @if($sem == $student->current_semester)
                                        <span class="ms-2 px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded-full border border-emerald-300">ACTIVE CURRENT SEMESTER</span>
                                    @elseif($sem < $student->current_semester)
                                        <span class="ms-2 px-2 py-0.5 bg-indigo-100 text-indigo-800 text-[10px] font-extrabold rounded-md">COMPLETED & STORED IN REPOSITORY</span>
                                    @else
                                        <span class="ms-2 px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-md">UPCOMING SEMESTER</span>
                                    @endif
                                </h4>
                                <p class="text-[11px] text-slate-500">
                                    Total Registered: {{ $grades->count() }} Courses • Total Credits: {{ $semData['total_credits'] }} Hrs
                                </p>
                            </div>
                        </div>

                        @if($sem < $student->current_semester)
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-slate-100 text-slate-700 font-extrabold text-xs rounded-xl border border-slate-200">
                                    Semester GPA: <span class="text-indigo-600 font-black">{{ $semData['gpa'] }}</span>
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Courses Table for this Semester -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-600">
                            <thead class="bg-slate-100/60 text-slate-700 font-bold uppercase border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-3">Course Code & Title</th>
                                    <th class="px-6 py-3">Credit Hours</th>
                                    <th class="px-6 py-3">Assigned Teacher</th>
                                    <th class="px-6 py-3 text-center">Marks Breakdown</th>
                                    <th class="px-6 py-3 text-center">Letter Grade</th>
                                    <th class="px-6 py-3 text-center">GPA Point</th>
                                    <th class="px-6 py-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($grades as $gradeRecord)
                                    @php
                                        $offering = $gradeRecord->courseOffering;
                                        $course = $offering->course ?? null;
                                        $gradeLetter = strtoupper(trim($gradeRecord->grade ?? ''));
                                        $isPassed = $gradeLetter && !in_array($gradeLetter, ['F', 'W', 'I']);
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-6 py-4 font-bold text-slate-900">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-900 text-[10px] font-extrabold rounded-lg font-mono">
                                                    {{ $course->course_code ?? 'COURSE' }}
                                                </span>
                                                <span>{{ $course->title ?? 'Course Title' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-slate-700 font-mono">
                                            {{ $course->credit_hours ?? 3 }} Cr.Hrs
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-bold text-slate-800 block">{{ $offering->teacher->name ?? 'Faculty Member' }}</span>
                                            <span class="text-[10px] text-slate-400 block">{{ $offering->academicSession->name ?? '' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono text-[11px]">
                                            @if($gradeRecord->total_marks)
                                                <span class="font-bold text-slate-800">Total: {{ number_format($gradeRecord->total_marks, 1) }}/100</span>
                                                <span class="block text-[9px] text-slate-400">Mid: {{ $gradeRecord->mid_marks ?? 0 }} | Int: {{ $gradeRecord->internal_marks ?? 0 }} | Fin: {{ $gradeRecord->final_marks ?? 0 }}</span>
                                            @else
                                                <span class="text-slate-400 font-normal">Marks Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($gradeLetter)
                                                <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $isPassed ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                                    {{ $gradeLetter }}
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded">Enrolled</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono font-bold text-slate-800">
                                            {{ number_format($gradeRecord->gpa_point ?? 0, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold">
                                            @if($sem < $student->current_semester)
                                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold rounded-lg inline-flex items-center gap-1 border border-emerald-200">
                                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Passed & Archived
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 bg-amber-100 text-amber-900 text-[10px] font-extrabold rounded-lg inline-flex items-center gap-1 border border-amber-200">
                                                    <i class="fa-solid fa-spinner animate-spin text-amber-600"></i> Currently In Progress
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-6 text-center text-slate-400 text-xs font-medium">
                                            @if($sem < $student->current_semester)
                                                No course record archived for Semester {{ $sem }}.
                                            @elseif($sem == $student->current_semester)
                                                Active current semester offerings available under the "Active Semester Courses" tab.
                                            @else
                                                Future semester course offerings will appear here upon registration.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endfor
    </div>

    <!-- TAB 2: Active Current Semester Courses Grid -->
    <div x-show="courseTab === 'active'" class="space-y-4" style="display: none;">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($activeOfferings as $offering)
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-emerald-600 text-white font-extrabold text-xs rounded-xl shadow-xs">{{ $offering->course->course_code }}</span>
                        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-xl border border-slate-200">{{ $offering->course->credit_hours }} Credit Hours</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-base text-slate-900">{{ $offering->course->title }}</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $offering->course->description ?? 'Core degree program course offering for current active semester.' }}</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <p class="text-slate-400 font-medium">Assigned Instructor:</p>
                            <p class="font-bold text-slate-800 mt-0.5">
                                {{ $offering->teacher ? $offering->teacher->name : 'Faculty Assigned' }}
                            </p>
                            @if($offering->teacher && $offering->teacher->email)
                                <p class="text-[10px] text-emerald-600">{{ $offering->teacher->email }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 bg-purple-100 text-purple-800 font-extrabold rounded-lg text-[10px] block border border-purple-200">
                                Semester {{ $offering->semester_number }} Active
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-1">
                                {{ $offering->program->name ?? '' }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white p-8 rounded-2xl border border-slate-200 text-center text-slate-400 text-xs">
                    No active courses currently enrolled for Semester {{ $student->current_semester }}.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
