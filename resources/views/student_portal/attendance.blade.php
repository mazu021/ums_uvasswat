@extends('layouts.app')

@section('title', 'My Attendance Record')
@section('header_title', 'Student Daily Attendance History')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Class Attendance Summary</h3>
            <p class="text-xs text-slate-500">Track daily check-in records, class presence, and minimum attendance eligibility threshold (75%).</p>
        </div>
        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">
            Overall: {{ $percentage }}% Attendance Rate
        </span>
    </div>

    <!-- Attendance Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 text-xs">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-400 uppercase">Total Classes Conducted</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalClasses }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-list-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-400 uppercase">Present Days</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $present }} Days</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-400 uppercase">Late Arrivals</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $late }} Days</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-400 uppercase">Absent Days</p>
                <h3 class="text-2xl font-bold text-red-500 mt-1">{{ $absent }} Days</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
    </div>

    <!-- Detailed Daily Attendance Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-slate-50 font-bold text-slate-800 text-sm">
            Detailed Daily Attendance Log
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Check In Time</th>
                        <th class="px-6 py-3">Check Out Time</th>
                        <th class="px-6 py-3">Attendance Status</th>
                        <th class="px-6 py-3">Instructor Notes / Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $att->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $att->check_in ?? '08:00 AM' }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $att->check_out ?? '04:00 PM' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                    {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $att->status === 'late' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $att->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $att->status === 'on_leave' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $att->notes ?? 'Regular class session' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-slate-400">No attendance logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-slate-50">
            {{ $attendances->links() }}
        </div>
    </div>

</div>
@endsection
