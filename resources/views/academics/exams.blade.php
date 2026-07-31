@extends('layouts.app')

@section('title', 'Examination & Class Gradebook')
@section('header_title', 'Course Examination & Class Gradebook')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-navy-900 to-indigo-950 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[11px] font-bold rounded-full">
                    <i class="fa-solid fa-calculator me-1"></i> Weightage: Mid (30%) • Internal (20%) • Final (50%)
                </span>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">Course Examination Gradebook</h1>
            <p class="text-slate-300 text-xs mt-1">
                Select your assigned course below to enter and update student examination marks progressively.
            </p>
        </div>

        @if($selectedOffering)
            <a href="{{ route('academics.exams.export-gradebook', $selectedOffering->id) }}" 
               class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center space-x-2 shrink-0">
                <i class="fa-solid fa-file-excel text-sm"></i>
                <span>Export Class Result (Excel)</span>
            </a>
        @endif
    </div>

    <!-- Assigned Courses Selection Cards (Interactive Hub) -->
    <div class="space-y-4" x-data="{ search: '' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-book-open text-emerald-600"></i>
                <span>Assigned Courses & Classes ({{ $offerings->count() }})</span>
            </h2>

            <!-- Quick Live Filter Input -->
            <div class="relative w-full sm:w-72">
                <input type="text" 
                       x-model="search" 
                       placeholder="Filter course, program, or teacher..." 
                       class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($offerings as $off)
                @php
                    $isSelected = $selectedOffering && $selectedOffering->id == $off->id;
                    $stCount = $off->getEnrolledStudents()->count();
                    $teacherName = $off->teacher ? ($off->teacher->name ?? $off->teacher->full_name) : 'Faculty Member';
                    $searchData = strtolower(($off->course->code ?? '') . ' ' . ($off->course->title ?? '') . ' ' . ($off->program->name ?? '') . ' ' . $teacherName);
                @endphp
                <a href="{{ route('academics.exams.index', ['course_offering_id' => $off->id]) }}" 
                   x-show="search === '' || {{ json_encode($searchData) }}.includes(search.toLowerCase())"
                   class="p-4 rounded-2xl border transition-all duration-200 block {{ $isSelected ? 'bg-emerald-50/90 border-emerald-500 ring-2 ring-emerald-500/30 shadow-md' : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-xs' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-0.5 rounded-lg font-extrabold text-[11px] {{ $isSelected ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-800 border border-slate-200' }}">
                            {{ $off->course->code ?? 'COURSE' }}
                        </span>
                        <span class="text-[11px] font-bold text-slate-500">
                            Sem {{ $off->semester_number ?? 1 }}
                        </span>
                    </div>

                    <h3 class="font-bold text-sm text-slate-900 leading-tight line-clamp-1 mb-1">
                        {{ $off->course->title ?? 'Course Title' }}
                    </h3>

                    <p class="text-[11px] text-slate-500 font-medium truncate mb-1">
                        <i class="fa-solid fa-graduation-cap me-1 text-slate-400"></i> {{ $off->program->name ?? 'Degree Program' }}
                    </p>

                    <p class="text-[11px] text-indigo-600 font-semibold truncate mb-3">
                        <i class="fa-solid fa-user-tie me-1"></i> Instructor: {{ $teacherName }}
                    </p>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="font-bold {{ $isSelected ? 'text-emerald-700' : 'text-slate-600' }}">
                            <i class="fa-solid fa-users me-1"></i> {{ $stCount }} Students
                        </span>
                        @if($isSelected)
                            <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Active Gradebook
                            </span>
                        @else
                            <span class="text-[11px] font-semibold text-slate-400 group-hover:text-slate-600">
                                Open Sheet &rarr;
                            </span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-full p-8 bg-white rounded-2xl border border-slate-200 text-center text-slate-400">
                    <i class="fa-solid fa-folder-open text-3xl block mb-2 opacity-30"></i>
                    No active course offerings assigned to your account.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Gradebook Sheet Form -->
    @if($selectedOffering)
        <form action="{{ route('academics.exams.save-gradebook') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            @csrf
            <input type="hidden" name="course_offering_id" value="{{ $selectedOffering->id }}">

            <!-- Gradebook Table Header Details -->
            <div class="p-5 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="font-extrabold text-base flex items-center gap-2">
                        <i class="fa-solid fa-book-open text-emerald-400"></i>
                        <span>{{ $selectedOffering->course->code ?? '' }} - {{ $selectedOffering->course->title ?? '' }}</span>
                    </h3>
                    <p class="text-xs text-slate-300 mt-0.5">
                        {{ $selectedOffering->program->name ?? 'Degree Program' }} • Semester {{ $selectedOffering->semester_number ?? 1 }} • Enrolled: {{ $students->count() }} Students
                    </p>
                </div>

                <div class="flex items-center space-x-3 text-xs">
                    <span class="px-3 py-1 bg-white/10 rounded-lg text-slate-300">
                        <i class="fa-solid fa-weight-scale me-1 text-emerald-400"></i> Mid: 30% | Internal: 20% | Final: 50%
                    </span>
                    <button type="submit" class="px-5 py-2 bg-emerald-500 hover:bg-emerald-400 text-white font-extrabold rounded-xl shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Gradebook</span>
                    </button>
                </div>
            </div>

            <!-- Gradebook Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[750px]">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="px-3 py-3 w-12 text-center">S.No</th>
                            <th class="px-3 py-3 w-36">Roll / Reg No</th>
                            <th class="px-3 py-3">Student Name</th>
                            <th class="px-3 py-3 w-28 text-center bg-blue-50/70 border-x border-blue-100 text-blue-900">
                                Mid Exam <br><span class="text-[9px] font-semibold text-blue-600">(Max 30)</span>
                            </th>
                            <th class="px-3 py-3 w-28 text-center bg-amber-50/70 border-x border-amber-100 text-amber-900">
                                Internal <br><span class="text-[9px] font-semibold text-amber-600">(Max 20)</span>
                            </th>
                            <th class="px-3 py-3 w-28 text-center bg-purple-50/70 border-x border-purple-100 text-purple-900">
                                Final Exam <br><span class="text-[9px] font-semibold text-purple-600">(Max 50)</span>
                            </th>
                            <th class="px-3 py-3 w-24 text-center font-extrabold text-slate-900">
                                Total <br><span class="text-[9px] font-semibold text-slate-500">(100)</span>
                            </th>
                            <th class="px-3 py-3 w-20 text-center">Grade</th>
                            <th class="px-3 py-3 w-20 text-center">GPA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($students as $index => $st)
                            @php 
                                $g = $gradesMap[$st->id] ?? null;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition" id="row_student_{{ $st->id }}">
                                <td class="px-3 py-3 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-3 py-3 font-mono font-bold text-slate-800">
                                    {{ $st->roll_number ?? ($st->registration_number ?? 'N/A') }}
                                </td>
                                <td class="px-3 py-3 font-bold text-slate-900">
                                    {{ $st->full_name }}
                                </td>
                                
                                <!-- Mid Marks (Max 30) -->
                                <td class="px-2 py-2 bg-blue-50/30 border-x border-blue-50">
                                    <input type="number" 
                                           step="0.5" 
                                           min="0" 
                                           max="30" 
                                           name="grades[{{ $st->id }}][mid_marks]" 
                                           value="{{ $g && $g->mid_marks !== null ? $g->mid_marks : '' }}"
                                           placeholder="Max 30"
                                           oninput="recalculateRow({{ $st->id }})"
                                           id="mid_{{ $st->id }}"
                                           class="w-full px-2 py-1.5 bg-white border border-blue-200 rounded-lg text-center font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:outline-none text-xs">
                                </td>

                                <!-- Internal Marks (Max 20) -->
                                <td class="px-2 py-2 bg-amber-50/30 border-x border-amber-50">
                                    <input type="number" 
                                           step="0.5" 
                                           min="0" 
                                           max="20" 
                                           name="grades[{{ $st->id }}][internal_marks]" 
                                           value="{{ $g && $g->internal_marks !== null ? $g->internal_marks : '' }}"
                                           placeholder="Max 20"
                                           oninput="recalculateRow({{ $st->id }})"
                                           id="internal_{{ $st->id }}"
                                           class="w-full px-2 py-1.5 bg-white border border-amber-200 rounded-lg text-center font-bold text-slate-900 focus:ring-2 focus:ring-amber-500 focus:outline-none text-xs">
                                </td>

                                <!-- Final Marks (Max 50) -->
                                <td class="px-2 py-2 bg-purple-50/30 border-x border-purple-50">
                                    <input type="number" 
                                           step="0.5" 
                                           min="0" 
                                           max="50" 
                                           name="grades[{{ $st->id }}][final_marks]" 
                                           value="{{ $g && $g->final_marks !== null ? $g->final_marks : '' }}"
                                           placeholder="Max 50"
                                           oninput="recalculateRow({{ $st->id }})"
                                           id="final_{{ $st->id }}"
                                           class="w-full px-2 py-1.5 bg-white border border-purple-200 rounded-lg text-center font-bold text-slate-900 focus:ring-2 focus:ring-purple-500 focus:outline-none text-xs">
                                </td>

                                <!-- Total Calculated -->
                                <td class="px-3 py-3 text-center font-extrabold text-slate-900 text-sm" id="total_{{ $st->id }}">
                                    {{ $g && $g->total_marks !== null ? number_format($g->total_marks, 1) : '-' }}
                                </td>

                                <!-- Letter Grade -->
                                <td class="px-3 py-3 text-center" id="grade_cell_{{ $st->id }}">
                                    <span class="px-2.5 py-1 font-extrabold rounded-md text-xs {{ $g && $g->grade == 'F' ? 'bg-rose-100 text-rose-800' : ($g && $g->grade ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400') }}" id="grade_badge_{{ $st->id }}">
                                        {{ $g->grade ?? '-' }}
                                    </span>
                                </td>

                                <!-- GPA Point -->
                                <td class="px-3 py-3 text-center font-bold text-slate-700" id="gpa_{{ $st->id }}">
                                    {{ $g && $g->gpa_point !== null ? number_format($g->gpa_point, 2) : '0.00' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-slate-400 font-medium">
                                    <i class="fa-solid fa-user-slash text-2xl block mb-2 opacity-40"></i>
                                    No students enrolled for this course offering and program yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Action Bar -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-500 font-medium">
                    <i class="fa-solid fa-circle-info me-1 text-indigo-500"></i> Marks are automatically converted to total %, letter grade, and grade points according to HEC standard rules.
                </span>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Submit & Save Class Gradebook</span>
                </button>
            </div>

        </form>
    @else
        <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-500 space-y-3">
            <i class="fa-solid fa-folder-open text-4xl text-slate-300"></i>
            <h3 class="font-bold text-base text-slate-700">No Active Course Offering Selected</h3>
            <p class="text-xs text-slate-400">Please select an assigned course from the cards above to load student rosters.</p>
        </div>
    @endif

</div>

@push('scripts')
<script>
    function recalculateRow(studentId) {
        var midVal = parseFloat(document.getElementById('mid_' + studentId).value) || null;
        var intVal = parseFloat(document.getElementById('internal_' + studentId).value) || null;
        var finVal = parseFloat(document.getElementById('final_' + studentId).value) || null;

        var totalCell = document.getElementById('total_' + studentId);
        var gradeBadge = document.getElementById('grade_badge_' + studentId);
        var gpaCell = document.getElementById('gpa_' + studentId);

        if (midVal === null && intVal === null && finVal === null) {
            totalCell.innerText = '-';
            gradeBadge.innerText = '-';
            gradeBadge.className = 'px-2.5 py-1 font-extrabold rounded-md text-xs bg-slate-100 text-slate-400';
            gpaCell.innerText = '0.00';
            return;
        }

        var sum = (midVal || 0) + (intVal || 0) + (finVal || 0);
        sum = Math.min(100.0, Math.max(0.0, sum));

        totalCell.innerText = sum.toFixed(1);

        var grade = 'F';
        var gpa = '0.00';

        if (sum >= 90) { grade = 'A+'; gpa = '4.00'; }
        else if (sum >= 80) { grade = 'A'; gpa = '3.70'; }
        else if (sum >= 75) { grade = 'B+'; gpa = '3.30'; }
        else if (sum >= 70) { grade = 'B'; gpa = '3.00'; }
        else if (sum >= 65) { grade = 'C+'; gpa = '2.50'; }
        else if (sum >= 60) { grade = 'C'; gpa = '2.00'; }
        else if (sum >= 50) { grade = 'D'; gpa = '1.00'; }
        else { grade = 'F'; gpa = '0.00'; }

        gradeBadge.innerText = grade;
        if (grade === 'F') {
            gradeBadge.className = 'px-2.5 py-1 font-extrabold rounded-md text-xs bg-rose-100 text-rose-800';
        } else {
            gradeBadge.className = 'px-2.5 py-1 font-extrabold rounded-md text-xs bg-emerald-100 text-emerald-800';
        }

        gpaCell.innerText = gpa;
    }
</script>
@endpush
@endsection
