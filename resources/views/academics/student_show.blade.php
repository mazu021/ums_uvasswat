@extends('layouts.app')

@section('title', $student->full_name . ' - Academic Transcript')
@section('header_title', 'Student Profile & Transcript')

@section('content')
<div class="space-y-6">

    <!-- Student Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-700 text-white font-bold flex items-center justify-center text-2xl shadow-lg">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $student->full_name }}</h2>
                <p class="text-xs font-semibold text-emerald-700">{{ $student->department->name }} • Semester {{ $student->current_semester }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Reg: {{ $student->registration_number }} | Roll: {{ $student->roll_number }}</p>
            </div>
        </div>
        <div class="flex space-x-2">
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">
                Status: {{ ucfirst($student->status) }}
            </span>
        </div>
    </div>

    <!-- Academic Transcript Section -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <div>
                <h3 class="font-bold text-base text-slate-900">Academic Examination Transcript</h3>
                <p class="text-xs text-slate-500">Official grades, marks obtained, and GPA summary.</p>
            </div>
            <button onclick="window.print()" class="px-3 py-1.5 bg-slate-900 text-white font-bold text-xs rounded-lg">
                <i class="fa-solid fa-print me-1"></i> Print Transcript
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-4 py-3">Course Code</th>
                        <th class="px-4 py-3">Course Title</th>
                        <th class="px-4 py-3">Examination</th>
                        <th class="px-4 py-3">Marks</th>
                        <th class="px-4 py-3">Grade</th>
                        <th class="px-4 py-3">GPA Point</th>
                        <th class="px-4 py-3">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($student->examGrades as $grade)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-bold text-emerald-700">{{ $grade->exam->course->course_code }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $grade->exam->course->title }}</td>
                            <td class="px-4 py-3 font-medium text-slate-600">{{ $grade->exam->title }}</td>
                            <td class="px-4 py-3 font-bold">{{ $grade->marks_obtained }} / {{ $grade->exam->total_marks }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    {{ $grade->grade }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ number_format($grade->gpa_point, 2) }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $grade->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">No examination grades recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fee Challans History -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="font-bold text-base text-slate-900 border-b pb-2">Student Fee History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-4 py-3">Challan No</th>
                        <th class="px-4 py-3">Issue Date</th>
                        <th class="px-4 py-3">Due Date</th>
                        <th class="px-4 py-3">Total Amount</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($student->feeChallans as $fc)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-bold text-slate-800">{{ $fc->challan_number }}</td>
                            <td class="px-4 py-3">{{ $fc->issue_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ $fc->due_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">Rs. {{ number_format($fc->total_amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $fc->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($fc->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('finance.fees.challans.print', $fc->id) }}" target="_blank" class="px-2.5 py-1 bg-slate-800 text-white font-bold rounded text-[10px]">
                                    Print Challan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">No fee challans generated.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
