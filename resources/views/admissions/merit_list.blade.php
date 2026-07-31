@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Official Merit List</h1>
            <p class="text-sm text-slate-500">UVAS Swat Admissions Merit Standing Directory.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-medium transition shadow-sm flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Merit List</span>
            </button>
        </div>
    </div>

    <!-- Program Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('admissions.merit-list') }}" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Select Program</label>
                <select name="program_id" onchange="this.form.submit()" class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500">
                    <option value="">All Programs Combined Merit List</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ $programId == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->code }})</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Merit List Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-emerald-900 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/uvas_logo.png') }}" class="w-8 h-8 rounded-full bg-white p-0.5">
                <div>
                    <h2 class="font-bold text-base">UVAS SWAT ADMISSIONS - MERIT LIST {{ date('Y') }}</h2>
                    <p class="text-xs text-emerald-300">Generated on: {{ date('F d, Y') }}</p>
                </div>
            </div>
            <span class="text-xs bg-emerald-800 text-emerald-100 font-semibold px-3 py-1 rounded-full">{{ count($applications) }} Candidates</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase">
                        <th class="px-6 py-3.5 text-center">Merit #</th>
                        <th class="px-6 py-3.5">Application #</th>
                        <th class="px-6 py-3.5">Candidate Name</th>
                        <th class="px-6 py-3.5">Father Name</th>
                        <th class="px-6 py-3.5">Program</th>
                        <th class="px-6 py-3.5 text-center">Matric %</th>
                        <th class="px-6 py-3.5 text-center">FSc %</th>
                        <th class="px-6 py-3.5 text-center">Entry Test %</th>
                        <th class="px-6 py-3.5 text-center bg-emerald-50 text-emerald-900">Merit Score</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($applications as $index => $app)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 text-center font-bold text-slate-800">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-mono text-xs font-bold text-emerald-700">{{ $app->application_no }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $app->applicant_name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $app->father_name }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $app->program->code ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center text-xs">{{ number_format(($app->matric_marks / max($app->matric_total, 1)) * 100, 1) }}%</td>
                        <td class="px-6 py-4 text-center text-xs">{{ number_format(($app->inter_marks / max($app->inter_total, 1)) * 100, 1) }}%</td>
                        <td class="px-6 py-4 text-center text-xs">{{ number_format(($app->entry_test_marks / max($app->entry_test_total, 1)) * 100, 1) }}%</td>
                        <td class="px-6 py-4 text-center font-extrabold text-base text-emerald-700 bg-emerald-50/50">{{ number_format($app->merit_score, 2) }}%</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">
                                {{ strtoupper($app->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-slate-500">No applicants found for the merit list.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
