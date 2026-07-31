@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Course Registration</h1>
            <p class="text-sm text-slate-500">Self-service course enrollment portal for active academic terms.</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg text-emerald-800 text-sm font-semibold">
            Enrolled Credit Hours: <span class="text-emerald-700 font-extrabold text-base">{{ $enrolledCreditHours }}</span> / 21
        </div>
    </div>

    <!-- Available Courses Selection -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
        <h2 class="text-base font-bold text-slate-800 border-b border-slate-200 pb-2">Select Course for Registration</h2>
        <form method="POST" action="{{ route('academics.course-registration.store') }}" class="flex flex-col sm:flex-row items-center gap-4">
            @csrf
            <div class="flex-1 w-full">
                <select name="course_id" required class="w-full rounded-lg border-slate-300 text-sm focus:ring-emerald-500">
                    <option value="">Select Course from Curriculum Catalog...</option>
                    @foreach($availableCourses as $course)
                        <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }} ({{ $course->credit_hours ?? 3 }} Credit Hours)</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition shadow-md w-full sm:w-auto">
                Request Enrollment
            </button>
        </form>
    </div>

    <!-- Requested / Enrolled Courses Directory -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Your Registration History</h2>
            <span class="text-xs bg-slate-100 text-slate-600 font-semibold px-3 py-1 rounded-full">{{ count($registrations) }} Courses</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                        <th class="px-6 py-3.5">Course Code</th>
                        <th class="px-6 py-3.5">Course Title</th>
                        <th class="px-6 py-3.5 text-center">Credit Hours</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5">Approval Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($registrations as $reg)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-mono font-bold text-slate-800">{{ $reg->course->code ?? 'N/A' }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $reg->course->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center font-bold text-slate-700">{{ $reg->course->credit_hours ?? 3 }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $badge = match($reg->status) {
                                    'approved' => 'bg-emerald-100 text-emerald-800',
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'rejected' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-slate-100 text-slate-800'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                                {{ strtoupper($reg->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">
                            @if($reg->approved_by)
                                Approved by {{ $reg->approved_by }}
                            @else
                                Pending HOD review
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No course registration requests submitted yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
