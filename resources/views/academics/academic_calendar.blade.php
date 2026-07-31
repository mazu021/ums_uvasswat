@extends('layouts.app')

@section('title', 'Official Academic Calendar')
@section('header_title', 'University Academic Calendar & Yearly Repository')

@section('content')
<div class="space-y-6" x-data="{ uploadModal: false }">

    <!-- Top Header Banner & Filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Official Academic Calendar</h3>
            <p class="text-xs text-slate-500">View and download official university yearly academic calendars (2024-2025, 2025-2026, 2026-2027, etc.).</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Academic Session / Year Filter Dropdown -->
            <form method="GET" action="{{ route('academics.calendar.index') }}" class="flex items-center space-x-2">
                <label class="text-xs font-extrabold text-slate-700 whitespace-nowrap">Academic Year:</label>
                <select name="session" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 shadow-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">-- Select Academic Year --</option>
                    @php
                        $uniqueSessions = $allCalendars->pluck('session_name')->unique()->values();
                    @endphp
                    @forelse($uniqueSessions as $sess)
                        <option value="{{ $sess }}" {{ ($selectedCalendar && $selectedCalendar->session_name == $sess) || $selectedSession == $sess ? 'selected' : '' }}>
                            Academic Year {{ $sess }}
                        </option>
                    @empty
                        <option value="2026-2027" selected>Academic Year 2026-2027</option>
                        <option value="2025-2026">Academic Year 2025-2026</option>
                        <option value="2024-2025">Academic Year 2024-2025</option>
                    @endforelse
                </select>
            </form>

            @if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin') || Auth::user()->can('manage settings'))
                <button @click="uploadModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Upload Yearly Calendar</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Yearly Calendar Selector Cards / Repository Tabs -->
    @if($allCalendars->count() > 0)
        <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Uploaded Yearly Academic Calendars:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($allCalendars as $cal)
                    @php
                        $isSelected = $selectedCalendar && $selectedCalendar->id == $cal->id;
                    @endphp
                    <a href="{{ route('academics.calendar.index', ['session' => $cal->session_name]) }}" class="px-4 py-2 rounded-2xl text-xs font-extrabold transition flex items-center space-x-2 {{ $isSelected ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        <i class="fa-solid fa-calendar-check text-[11px] {{ $isSelected ? 'text-emerald-400' : 'text-slate-400' }}"></i>
                        <span>Session {{ $cal->session_name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Active Calendar Document Details Header -->
    @if($selectedCalendar)
        <div class="p-6 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 border border-slate-800">
            <div class="space-y-1.5 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start space-x-2">
                    <span class="px-3 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-extrabold rounded-full uppercase tracking-wider">
                        <i class="fa-solid fa-circle-check text-[9px] me-1"></i> Academic Session {{ $selectedCalendar->session_name }}
                    </span>
                </div>
                <h2 class="text-2xl font-black text-white tracking-tight">{{ $selectedCalendar->title }}</h2>
                <p class="text-xs text-slate-300 font-medium">
                    Published on: <span class="font-bold text-white">{{ $selectedCalendar->created_at->format('M d, Y') }}</span>
                    @if($selectedCalendar->uploader)
                        | Uploaded by: <span class="text-emerald-300 font-bold">{{ $selectedCalendar->uploader->name }}</span>
                    @endif
                </p>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ asset('storage/' . $selectedCalendar->file_path) }}" target="_blank" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 font-bold text-xs rounded-xl backdrop-blur transition flex items-center space-x-2">
                    <i class="fa-solid fa-up-right-from-square"></i>
                    <span>Fullscreen</span>
                </a>
                <a href="{{ asset('storage/' . $selectedCalendar->file_path) }}" download class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-download"></i>
                    <span>Download PDF</span>
                </a>

                @if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin'))
                    <form action="{{ route('academics.calendar.destroy', $selectedCalendar->id) }}" method="POST" onsubmit="return confirm('Delete this calendar for session {{ $selectedCalendar->session_name }}?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2.5 bg-rose-600/80 hover:bg-rose-600 text-white font-bold rounded-xl transition" title="Delete Calendar">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Published Document Viewer -->
        @php
            $ext = strtolower(pathinfo($selectedCalendar->file_path, PATHINFO_EXTENSION));
        @endphp
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h4 class="font-extrabold text-sm text-slate-900 flex items-center space-x-2">
                    <i class="fa-solid fa-file-pdf text-rose-600 text-base"></i>
                    <span>Academic Calendar Preview (Session {{ $selectedCalendar->session_name }})</span>
                </h4>
                <span class="text-xs font-mono text-slate-400 uppercase font-bold">{{ $ext }} File</span>
            </div>

            @if(in_array($ext, ['pdf']))
                <div class="w-full h-[750px] rounded-2xl overflow-hidden border border-slate-200 bg-slate-100">
                    <iframe src="{{ asset('storage/' . $selectedCalendar->file_path) }}" class="w-full h-full border-0"></iframe>
                </div>
            @else
                <div class="flex items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <img src="{{ asset('storage/' . $selectedCalendar->file_path) }}" alt="Academic Calendar" class="max-w-full h-auto rounded-xl shadow-md border">
                </div>
            @endif
        </div>
    @else
        <!-- Empty State Alert -->
        <div class="p-12 bg-white rounded-3xl border border-slate-200 shadow-sm text-center space-y-4">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-3xl mx-auto flex items-center justify-center text-2xl shadow-xs border border-emerald-100">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="space-y-1">
                <h4 class="font-extrabold text-slate-900 text-base">No Academic Calendar Uploaded For Selected Year</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                    Please select another academic year from the dropdown above or upload the official calendar for this session.
                </p>
            </div>
            @if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin') || Auth::user()->can('manage settings'))
                <button @click="uploadModal = true" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow inline-flex items-center space-x-2 transition">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Upload Calendar Now</span>
                </button>
            @endif
        </div>
    @endif

    <!-- Upload Modal (For Administrators) -->
    @if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin') || Auth::user()->can('manage settings'))
    <div x-show="uploadModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="uploadModal = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-emerald-400"></i>
                    <span>Upload Yearly Academic Calendar</span>
                </h4>
                <button @click="uploadModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('academics.calendar.upload') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Academic Session / Year *</label>
                    <input type="text" name="session_name" required placeholder="e.g. 2024-2025, 2025-2026, 2026-2027" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">Specify the session (e.g. 2024-2025, 2025-2026, 2026-2027).</p>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Calendar Document Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Official Academic Calendar 2026-2027" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select Document File (PDF / Image) *</label>
                    <input type="file" name="calendar_file" required accept=".pdf,.png,.jpg,.jpeg,.webp" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    <p class="text-[10px] text-slate-400 mt-1">Supported formats: PDF, PNG, JPG (Max 10MB)</p>
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="uploadModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow transition">Upload & Save Year</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
