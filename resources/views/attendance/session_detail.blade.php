@extends('layouts.app')

@section('title', 'Attendance Sheet Detail')
@section('header_title', 'Attendance Sheet Log')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-extrabold text-xs rounded-lg">
                    Lecture #{{ $attendanceSession->lecture_number }}
                </span>
                <span class="text-xs font-semibold text-slate-500">
                    {{ $attendanceSession->attendance_date->format('l, F d, Y') }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-2">
                {{ $attendanceSession->courseOffering->course->title }} ({{ $attendanceSession->courseOffering->course->course_code }})
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Topic: <strong>{{ $attendanceSession->topic ?? 'Not specified' }}</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('attendance.offering.history', $attendanceSession->course_offering_id) }}" class="px-4 py-2.5 bg-slate-800 text-white font-bold text-xs rounded-xl hover:bg-slate-900 transition">
                Back to Session History
            </a>
        </div>
    </div>

    <!-- Student Attendance List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-xs text-slate-700 flex justify-between items-center">
            <span>Student Attendance Log ({{ $attendanceSession->records->count() }} Records)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Roll Number</th>
                        <th class="py-3 px-4">Student Name</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($attendanceSession->records as $idx => $record)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-4 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                        <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $record->student->roll_number ?? 'N/A' }}</td>
                        <td class="py-3 px-4 font-bold text-slate-800">{{ $record->student->full_name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($record->status === 'Present')
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 font-extrabold rounded-lg text-[10px]">Present</span>
                            @elseif($record->status === 'Absent')
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 font-extrabold rounded-lg text-[10px]">Absent</span>
                            @elseif($record->status === 'Leave')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 font-extrabold rounded-lg text-[10px]">Leave</span>
                            @else
                                <span class="px-2.5 py-1 bg-purple-100 text-purple-700 font-extrabold rounded-lg text-[10px]">Late</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-500">{{ $record->remarks ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
