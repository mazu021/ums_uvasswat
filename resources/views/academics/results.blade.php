@extends('layouts.app')

@section('title', 'Examination Results & Transcripts')
@section('header_title', 'Institutional Examination Results & Grade Repository')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Examination Results Repository</h3>
            <p class="text-xs text-slate-500">View official student course grades, GPA calculations, marks breakdown, and transcripts.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('academics.exams.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Marks & Gradebook Entry</span>
            </a>
            <a href="{{ route('academics.transcript') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Download Official Transcript</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('academics.results.index') }}" class="w-full flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <input type="text" name="search" value="{{ $search }}" placeholder="Student Name, Reg No..." class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs w-56 font-semibold">
                <select name="program_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700">
                    <option value="">All Degree Programs</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ $p->id == $programId ? 'selected' : '' }}>{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
                <select name="semester" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700">
                    <option value="">All Semesters</option>
                    @for($s = 1; $s <= 10; $s++)
                        <option value="{{ $s }}" {{ $semester == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl shadow">Filter</button>
            </div>

            <div class="flex items-center space-x-2 text-xs font-bold text-slate-600">
                <span>Show Records:</span>
                <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 100) == 100 ? 'selected' : '' }}>100 (Default)</option>
                    <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Student Details</th>
                        <th class="px-6 py-4">Course & Code</th>
                        <th class="px-6 py-4">Semester</th>
                        <th class="px-6 py-4 text-center">Marks Breakdown</th>
                        <th class="px-6 py-4 text-center">Letter Grade</th>
                        <th class="px-6 py-4 text-center">GPA Point</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($results as $res)
                        @php
                            $student = $res->student;
                            $offering = $res->courseOffering;
                            $course = $offering->course ?? null;
                            $gradeLetter = strtoupper(trim($res->grade ?? ''));
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <span class="block text-slate-900">{{ $student->full_name ?? 'Student' }}</span>
                                <span class="text-[10px] font-mono text-emerald-600 block">{{ $student->registration_number ?? 'Reg No' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-900 font-mono text-[10px] font-bold rounded me-1">
                                    {{ $course->course_code ?? 'CODE' }}
                                </span>
                                <span class="font-bold text-slate-800">{{ $course->title ?? 'Course Title' }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700">
                                Sem {{ $offering->semester_number ?? 1 }}
                            </td>
                            <td class="px-6 py-4 text-center font-mono text-[11px]">
                                @if($res->total_marks)
                                    <span class="font-bold text-slate-900">Total: {{ number_format($res->total_marks, 1) }}</span>
                                    <span class="block text-[9px] text-slate-400">Mid: {{ $res->mid_marks ?? 0 }} | Int: {{ $res->internal_marks ?? 0 }} | Fin: {{ $res->final_marks ?? 0 }}</span>
                                @else
                                    <span class="text-slate-400">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($gradeLetter)
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ in_array($gradeLetter, ['F', 'W', 'I']) ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                        {{ $gradeLetter }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-bold">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-mono font-bold text-slate-900">
                                {{ number_format($res->gpa_point ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('academics.exams.transcript', $student->id ?? 1) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] rounded-lg border border-slate-200 transition">
                                    Transcript
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-xs">No exam results recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $results->links() }}
        </div>
    </div>

</div>
@endsection
