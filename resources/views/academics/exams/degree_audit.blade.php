@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Degree Audit & Graduation Clearance</h1>
            <p class="text-sm text-slate-500">Automated Graduation Requirements & Academic Audit Verification.</p>
        </div>
        <a href="{{ route('academics.exams.transcript', $student) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
            View Official Transcript
        </a>
    </div>

    <!-- Clearance Status Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl {{ $isClearForGraduation ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                <i class="fa-solid {{ $isClearForGraduation ? 'fa-user-graduate' : 'fa-clock' }}"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Graduation Status: {{ $isClearForGraduation ? 'ELIGIBLE FOR GRADUATION' : 'IN PROGRESS' }}</h2>
                <p class="text-sm text-slate-500">Candidate: <strong class="text-slate-800">{{ $student->user->name ?? 'N/A' }}</strong> (Reg #: {{ $student->registration_number }})</p>
            </div>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 font-bold uppercase">Current CGPA</span>
            <div class="text-3xl font-extrabold {{ $cgpa >= 2.00 ? 'text-emerald-600' : 'text-rose-600' }}">{{ number_format($cgpa, 2) }}</div>
        </div>
    </div>

    <!-- Audit Checklist Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Metric 1: Credit Hours -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase">Credit Hours</span>
                <i class="fa-solid {{ $completedCredits >= $requiredCredits ? 'fa-circle-check text-emerald-600' : 'fa-circle-xmark text-rose-500' }}"></i>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $completedCredits }} / {{ $requiredCredits }}</div>
            <p class="text-xs text-slate-500">Minimum {{ $requiredCredits }} credit hours required for degree completion.</p>
        </div>

        <!-- Metric 2: Minimum CGPA -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase">Minimum CGPA Criteria</span>
                <i class="fa-solid {{ $cgpa >= 2.00 ? 'fa-circle-check text-emerald-600' : 'fa-circle-xmark text-rose-500' }}"></i>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ number_format($cgpa, 2) }} / 2.00</div>
            <p class="text-xs text-slate-500">Minimum CGPA threshold for degree award is 2.00.</p>
        </div>

        <!-- Metric 3: Failing Courses -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase">Failing Grades ('F')</span>
                <i class="fa-solid {{ !$hasFailedCourses ? 'fa-circle-check text-emerald-600' : 'fa-circle-xmark text-rose-500' }}"></i>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $hasFailedCourses ? 'Found Failing Grade' : 'No Failing Grades' }}</div>
            <p class="text-xs text-slate-500">All required curriculum courses must be cleared.</p>
        </div>
    </div>
</div>
@endsection
