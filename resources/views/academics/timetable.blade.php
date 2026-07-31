@extends('layouts.app')

@section('title', 'Class Timetable & Schedule')
@section('header_title', 'Class Timetable & Room Schedule')

@section('content')
<div class="space-y-6" x-data="{ createModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Class Timetable & Schedule</h3>
            <p class="text-xs text-slate-500">Manage lecture time slots, weekly class schedules, assigned faculty, and lecture halls/labs.</p>
        </div>
        <button @click="createModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
            <i class="fa-solid fa-clock"></i>
            <span>Add Timetable Slot</span>
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Day of Week</th>
                        <th class="px-6 py-4">Time Slot</th>
                        <th class="px-6 py-4">Course & Code</th>
                        <th class="px-6 py-4">Assigned Teacher</th>
                        <th class="px-6 py-4">Room / Venue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($timetables as $slot)
                        @php
                            $offering = $slot->courseOffering;
                            $course = $offering->course ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-extrabold text-indigo-900">
                                <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-200 rounded-lg text-xs">
                                    {{ $slot->day_of_week ?? 'Monday' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-700">
                                {{ $slot->start_time }} - {{ $slot->end_time }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <span class="font-mono text-emerald-600 me-1">{{ $course->course_code ?? 'CODE' }}</span>
                                <span>{{ $course->title ?? 'Course Title' }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $offering->teacher->name ?? 'Faculty Assigned' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-amber-900">
                                {{ $slot->room_number ?? 'Lecture Hall A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs">No timetable schedule configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="createModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100" @click.away="createModal = false">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Add Timetable Slot</h4>
                <button @click="createModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('academics.timetable.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Course Offering *</label>
                    <select name="course_offering_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                        @foreach($offerings as $o)
                            <option value="{{ $o->id }}">{{ $o->course->course_code ?? '' }} - {{ $o->course->title ?? '' }} (Sem {{ $o->semester_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Day of Week *</label>
                        <select name="day_of_week" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Room / Venue</label>
                        <input type="text" name="room_number" placeholder="e.g. Lecture Hall 1" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Start Time *</label>
                        <input type="time" name="start_time" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">End Time *</label>
                        <input type="time" name="end_time" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
                    </div>
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl shadow">Save Slot</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
