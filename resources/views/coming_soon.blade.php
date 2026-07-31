@extends('layouts.app')

@section('title', $feature . ' - Coming Soon')
@section('header_title', $feature)

@section('content')
<div class="min-h-[70vh] flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-3xl border border-slate-200 shadow-xl p-8 text-center space-y-6">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl mx-auto flex items-center justify-center text-3xl shadow-inner border border-emerald-200 animate-bounce">
            <i class="fa-solid fa-rocket"></i>
        </div>

        <div class="space-y-2">
            <span class="px-3 py-1 bg-amber-100 text-amber-900 text-[10px] font-black rounded-full uppercase border border-amber-300">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> Under Active Development
            </span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $feature }}</h2>
            <p class="text-xs text-slate-500 leading-relaxed font-medium">
                The <strong class="text-slate-800">{{ $feature }}</strong> module is scheduled for full deployment in the upcoming UMS release. All foundational services are active.
            </p>
        </div>

        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-left text-xs space-y-2">
            <p class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-indigo-600"></i>
                <span>Module Status Overview:</span>
            </p>
            <ul class="text-[11px] text-slate-500 space-y-1 pl-5 list-disc font-medium">
                <li>Database Schema & Permissions Configured</li>
                <li>Module Scheduled for Version Update</li>
                <li>Core ERP Integration Ready</li>
            </ul>
        </div>

        <div class="pt-2 flex items-center justify-center space-x-3">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-house"></i>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </div>
</div>
@endsection
