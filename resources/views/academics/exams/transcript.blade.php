@extends('layouts.app')

@section('content')
<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Official Academic Transcript</h1>
            <p class="text-sm text-slate-500">Controller of Examinations Official Grade Record.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-medium transition flex items-center space-x-2">
            <i class="fa-solid fa-print"></i>
            <span>Print Official Transcript</span>
        </button>
    </div>

    <!-- Official Transcript Document Container -->
    <div class="bg-white rounded-xl shadow-lg border border-slate-200 p-8 space-y-6 relative overflow-hidden">
        <!-- University Watermark Header -->
        <div class="border-b-2 border-emerald-800 pb-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/uvas_logo.png') }}" class="w-20 h-20 object-contain">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 uppercase tracking-wide">University of Veterinary & Animal Sciences, Swat</h2>
                    <p class="text-xs font-semibold text-emerald-800 uppercase tracking-widest">Office of the Controller of Examinations</p>
                    <p class="text-xs text-slate-500">Kanju Township, Sector A, Swat, Khyber Pakhtunkhwa, Pakistan</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-900 text-xs font-extrabold rounded">OFFICIAL TRANSCRIPT</span>
                <p class="text-xs font-mono text-slate-500 mt-1">Date: {{ date('F d, Y') }}</p>
            </div>
        </div>

        <!-- Student Information Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-slate-50 rounded-lg border border-slate-200 text-sm">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase">Student Name</span>
                <p class="font-bold text-slate-800">{{ $student->user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase">Father Name</span>
                <p class="font-semibold text-slate-800">{{ $student->father_name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase">Registration No</span>
                <p class="font-mono font-bold text-emerald-700">{{ $student->registration_number }}</p>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase">Roll Number</span>
                <p class="font-mono font-semibold text-slate-800">{{ $student->roll_number }}</p>
            </div>
        </div>

        <!-- Academic Performance Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-emerald-900 text-white text-xs font-semibold uppercase">
                        <th class="px-4 py-3">Course Code</th>
                        <th class="px-4 py-3">Course Title</th>
                        <th class="px-4 py-3 text-center">Credit Hours</th>
                        <th class="px-4 py-3 text-center">Marks Obtained</th>
                        <th class="px-4 py-3 text-center">Grade</th>
                        <th class="px-4 py-3 text-center">Grade Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($grades as $grade)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono font-bold text-slate-800">{{ $grade->exam->course->code ?? 'N/A' }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $grade->exam->course->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-center font-bold text-slate-700">{{ $grade->exam->course->credit_hours ?? 3 }}</td>
                        <td class="px-4 py-3 text-center font-semibold text-slate-800">{{ $grade->marks_obtained }} / 100</td>
                        <td class="px-4 py-3 text-center font-extrabold text-emerald-700">{{ $grade->grade }}</td>
                        <td class="px-4 py-3 text-center font-mono font-bold">
                            @php
                                $pts = match($grade->grade) {
                                    'A+', 'A' => 4.0,
                                    'B+' => 3.5,
                                    'B' => 3.0,
                                    'C+' => 2.5,
                                    'C' => 2.0,
                                    'D' => 1.0,
                                    default => 0.0,
                                };
                            @endphp
                            {{ number_format($pts, 1) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-500">No examination grades recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- CGPA & Summary Box -->
        <div class="p-6 bg-slate-900 text-white rounded-xl flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="space-y-1">
                <span class="text-xs uppercase font-bold text-slate-400">Cumulative Academic Summary</span>
                <p class="text-xs text-slate-300">Total Credit Hours Completed: <strong class="text-white">{{ $totalCreditHours }}</strong> | Total Quality Points: <strong class="text-white">{{ $totalQualityPoints }}</strong></p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 uppercase font-bold">Cumulative GPA (CGPA)</span>
                <div class="text-4xl font-black text-emerald-400">{{ number_format($cgpa, 2) }} / 4.00</div>
            </div>
        </div>

        <!-- Official Signatures Footer -->
        <div class="pt-12 grid grid-cols-2 gap-8 text-center text-xs text-slate-600">
            <div class="border-t border-slate-300 pt-2">
                <p class="font-bold text-slate-800">Assistant Controller of Examinations</p>
                <p class="text-[10px] text-slate-400">UVAS Swat</p>
            </div>
            <div class="border-t border-slate-300 pt-2">
                <p class="font-bold text-slate-800">Controller of Examinations</p>
                <p class="text-[10px] text-slate-400">UVAS Swat</p>
            </div>
        </div>
    </div>
</div>
@endsection
