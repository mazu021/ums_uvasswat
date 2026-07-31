@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Examination Hall Seating Plan</h1>
            <p class="text-sm text-slate-500">Automated Seating Allocation Matrix for Controller of Examinations.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-medium transition flex items-center space-x-2">
            <i class="fa-solid fa-print"></i>
            <span>Print Seating Grid</span>
        </button>
    </div>

    <!-- Exam Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">{{ $exam->title }}</h2>
            <p class="text-sm text-slate-500">Course: <span class="font-semibold text-slate-700">{{ $exam->course->name ?? 'N/A' }} ({{ $exam->course->code ?? '' }})</span></p>
            <p class="text-xs text-slate-400">Date: {{ $exam->exam_date }} | Hall: {{ $seatingPlan->hall_name }}</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg text-xs text-emerald-800">
            <strong>Invigilator in Charge:</strong> {{ $seatingPlan->invigilator_name }}
        </div>
    </div>

    <!-- Visual Seating Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
        <div class="bg-slate-900 text-white text-center py-2.5 rounded-lg font-bold uppercase tracking-widest text-xs">
            FRONT / INVIGILATOR DAIS & PODIUM
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
            @foreach($seatingPlan->allocated_students as $seat)
            <div class="p-3 bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 rounded-lg text-center transition group">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">{{ $seat['seat'] }}</span>
                <div class="font-mono text-xs font-bold text-emerald-700 group-hover:text-emerald-800">{{ $seat['roll_number'] }}</div>
                <div class="text-xs font-semibold text-slate-800 truncate mt-0.5">{{ $seat['name'] }}</div>
            </div>
            @endforeach
        </div>

        <div class="bg-slate-100 text-slate-500 text-center py-2 rounded-lg text-xs font-semibold">
            REAR ENTRANCE / EXIT DOOR
        </div>
    </div>
</div>
@endsection
