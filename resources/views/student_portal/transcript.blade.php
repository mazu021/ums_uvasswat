@extends('layouts.app')

@section('title', 'My Official Academic Transcript')
@section('header_title', 'Download Official Transcript')

@push('styles')
<style>
    @media print {
        header, nav, sidebar, .no-print, button, form {
            display: none !important;
        }
        body {
            background-color: white !important;
            color: black !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .print-container {
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Action Bar & Print Button -->
    <div class="no-print bg-gradient-to-r from-slate-900 via-navy-900 to-indigo-950 p-6 rounded-2xl text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black">Official Academic Transcript</h1>
            <p class="text-xs text-slate-300 mt-0.5">Download or print your official semester evaluation & CGPA grade sheet.</p>
        </div>
        <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-white font-extrabold text-xs rounded-xl shadow-lg transition flex items-center space-x-2 shrink-0">
            <i class="fa-solid fa-file-pdf text-sm"></i>
            <span>Download Official Transcript (PDF)</span>
        </button>
    </div>

    <!-- Official Transcript Document -->
    <div class="print-container bg-white p-8 md:p-12 rounded-2xl border border-slate-200 shadow-xl max-w-4xl mx-auto space-y-8 text-slate-900">
        
        <!-- Official Header & University Crest -->
        <div class="border-b-2 border-slate-900 pb-6 text-center space-y-2">
            <div class="flex justify-center mb-2">
                <img src="{{ asset('images/uvas_logo.png') }}" alt="UVAS Swat Logo" class="h-20 w-auto object-contain">
            </div>
            <h1 class="text-xl md:text-2xl font-black uppercase tracking-wider text-slate-900">
                The University of Veterinary and Animal Sciences, Swat
            </h1>
            <p class="text-xs font-bold text-slate-600 tracking-wide uppercase">
                Office of the Controller of Examinations • Official Student Grade Sheet
            </p>
            <div class="inline-block px-4 py-1 bg-slate-900 text-white text-xs font-extrabold rounded-full uppercase tracking-widest mt-2">
                Academic Transcript
            </div>
        </div>

        <!-- Student Bio Details Box -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 p-5 bg-slate-50 rounded-xl border border-slate-200 text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Student Name</span>
                <strong class="text-sm font-extrabold text-slate-900">{{ $student->full_name }}</strong>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Registration Number</span>
                <strong class="font-mono text-slate-900 font-bold">{{ $student->registration_number ?? 'N/A' }}</strong>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Roll Number</span>
                <strong class="font-mono text-slate-900 font-bold">{{ $student->roll_number ?? 'N/A' }}</strong>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Degree Program</span>
                <strong class="text-slate-800 font-bold">{{ $student->program->name ?? $student->program_name }}</strong>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Current Semester</span>
                <strong class="text-slate-800 font-bold">Semester {{ $student->current_semester ?? 1 }}</strong>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Date of Issue</span>
                <strong class="text-slate-800 font-bold">{{ date('F d, Y') }}</strong>
            </div>
        </div>

        <!-- Academic Results Table -->
        <div class="space-y-3">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Semester Course Evaluation Breakdown</h3>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100 border-b border-slate-200 text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                            <th class="p-3 w-10 text-center">S.No</th>
                            <th class="p-3">Course Title</th>
                            <th class="p-3 text-center">Cr.Hr</th>
                            <th class="p-3 text-center">Mid (30)</th>
                            <th class="p-3 text-center">Internal (20)</th>
                            <th class="p-3 text-center">Final (50)</th>
                            <th class="p-3 text-center font-black text-slate-900">Total (100)</th>
                            <th class="p-3 text-center">Grade</th>
                            <th class="p-3 text-center">GPA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-800">
                        @forelse($grades as $idx => $g)
                            <tr>
                                <td class="p-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="p-3 font-bold">
                                    {{ $g->courseOffering->course->code ?? '' }}: {{ $g->courseOffering->course->title ?? 'Course Title' }}
                                </td>
                                <td class="p-3 text-center font-semibold">{{ $g->courseOffering->course->credit_hours ?? 3 }}</td>
                                <td class="p-3 text-center font-mono">{{ $g->mid_marks !== null ? number_format($g->mid_marks, 1) : '-' }}</td>
                                <td class="p-3 text-center font-mono">{{ $g->internal_marks !== null ? number_format($g->internal_marks, 1) : '-' }}</td>
                                <td class="p-3 text-center font-mono">{{ $g->final_marks !== null ? number_format($g->final_marks, 1) : '-' }}</td>
                                <td class="p-3 text-center font-mono font-extrabold text-slate-900">
                                    {{ $g->total_marks !== null ? number_format($g->total_marks, 1) : '-' }}
                                </td>
                                <td class="p-3 text-center font-extrabold">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $g->grade == 'F' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $g->grade ?? '-' }}
                                    </span>
                                </td>
                                <td class="p-3 text-center font-bold">{{ number_format($g->gpa_point, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-6 text-center text-slate-400 italic">
                                    No evaluation records submitted for your courses yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cumulative GPA Summary Box -->
        <div class="p-5 bg-slate-900 text-white rounded-xl flex flex-wrap items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Official Academic Standing</span>
                <h4 class="text-xl font-black text-emerald-400">
                    @if($cgpa >= 3.66)
                        DEAN'S HONOR ROLL (EXCELLENT)
                    @elseif($cgpa >= 3.00)
                        GOOD STANDING
                    @elseif($cgpa >= 2.00)
                        SATISFACTORY
                    @elseif($cgpa > 0)
                        ACADEMIC WARNING
                    @else
                        EVALUATION PENDING
                    @endif
                </h4>
            </div>
            <div class="flex items-center space-x-6 text-right">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-bold">Total Cr.Hours</span>
                    <strong class="text-base font-extrabold text-white">{{ $totalCreditHours }}</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-bold">Quality Points</span>
                    <strong class="text-base font-extrabold text-white">{{ number_format($totalQualityPoints, 2) }}</strong>
                </div>
                <div class="pl-4 border-l border-slate-700">
                    <span class="text-[10px] text-emerald-300 uppercase block font-extrabold">Cumulative GPA</span>
                    <strong class="text-2xl font-black text-white">{{ number_format($cgpa, 2) }} / 4.00</strong>
                </div>
            </div>
        </div>

        <!-- Official Signatures & Seal Section -->
        <div class="pt-12 border-t border-slate-200 grid grid-cols-2 gap-8 text-xs text-slate-600">
            <div class="space-y-10">
                <p class="text-[10px] font-medium text-slate-400 italic">
                    * Errors and omissions exempted. Generated automatically by UVAS Swat UMS Student Portal.
                </p>
                <div class="border-t border-slate-400 pt-2 w-48 text-center font-bold text-slate-800">
                    Prepared & Checked By
                </div>
            </div>
            <div class="space-y-10 text-right flex flex-col items-end">
                <div class="w-32 h-16 border-2 border-dashed border-slate-300 rounded-lg flex items-center justify-center text-[10px] text-slate-400 font-bold uppercase">
                    Official Seal
                </div>
                <div class="border-t border-slate-400 pt-2 w-48 text-center font-bold text-slate-900">
                    Controller of Examinations
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
