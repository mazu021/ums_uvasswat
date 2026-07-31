@extends('layouts.app')

@section('title', 'Mark Attendance')
@section('header_title', 'Attendance Sheet')

@section('content')
<div class="space-y-6" x-data="attendanceSheet()">

    <!-- Offering Info Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-extrabold text-xs rounded-lg">
                    {{ $courseOffering->course->course_code }}
                </span>
                <span class="text-xs font-semibold text-slate-500">
                    {{ $courseOffering->program->name }} (Sem {{ $courseOffering->semester_number }})
                </span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-2">{{ $courseOffering->course->title }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Batch: <strong>{{ $courseOffering->batch->name }}</strong> | Session: <strong>{{ $courseOffering->academicSession->name }}</strong>
                @if($courseOffering->section) | Section: <strong>{{ $courseOffering->section->name }}</strong> @endif
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('attendance.offering.history', $courseOffering->id) }}" class="px-3 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-200 transition">
                <i class="fa-solid fa-history me-1"></i> Session Logs
            </a>
            <a href="{{ route('attendance.teacher.dashboard') }}" class="px-3 py-2 bg-slate-800 text-white text-xs font-bold rounded-xl hover:bg-slate-900 transition">
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Attendance Form -->
    <form action="{{ route('attendance.mark.store', $courseOffering->id) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Lecture Metadata Header -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Attendance Date *</label>
                <input type="date" name="attendance_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Lecture Number *</label>
                <input type="number" name="lecture_number" value="{{ $nextLecture }}" min="1" max="20" required class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Lecture Topic (Optional)</label>
                <input type="text" name="topic" placeholder="e.g. Chapter 3: Fundamentals" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500">
            </div>
        </div>

        <!-- Quick Toggles Toolbar -->
        <div class="bg-slate-100 p-4 rounded-2xl flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="font-bold text-slate-700 flex items-center space-x-2">
                <i class="fa-solid fa-users text-emerald-600 text-base"></i>
                <span>Enrolled Students Auto-Loaded ({{ count($students) }})</span>
            </div>
            <div class="flex items-center space-x-2">
                <button type="button" @click="markAll('Present')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-xs transition">
                    Mark All Present
                </button>
                <button type="button" @click="markAll('Absent')" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-xs transition">
                    Mark All Absent
                </button>
            </div>
        </div>

        <!-- Student Attendance Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">#</th>
                            <th class="py-3.5 px-4">Roll Number</th>
                            <th class="py-3.5 px-4">Student Name</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($students as $index => $student)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $student->roll_number }}
                                <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $student->full_name }}</div>
                                <div class="text-[10px] text-slate-400">Reg: {{ $student->registration_number }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex rounded-xl p-1 bg-slate-100 space-x-1">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[{{ $index }}][status]" value="Present" x-model="statuses[{{ $student->id }}]" class="sr-only">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition inline-block"
                                              :class="statuses[{{ $student->id }}] === 'Present' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200'">
                                            Present
                                        </span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[{{ $index }}][status]" value="Absent" x-model="statuses[{{ $student->id }}]" class="sr-only">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition inline-block"
                                              :class="statuses[{{ $student->id }}] === 'Absent' ? 'bg-red-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200'">
                                            Absent
                                        </span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[{{ $index }}][status]" value="Leave" x-model="statuses[{{ $student->id }}]" class="sr-only">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition inline-block"
                                              :class="statuses[{{ $student->id }}] === 'Leave' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200'">
                                            Leave
                                        </span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[{{ $index }}][status]" value="Late" x-model="statuses[{{ $student->id }}]" class="sr-only">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition inline-block"
                                              :class="statuses[{{ $student->id }}] === 'Late' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200'">
                                            Late
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <input type="text" name="attendance[{{ $index }}][remarks]" placeholder="Note..." class="w-full rounded-lg border-slate-200 text-xs focus:ring-emerald-500 py-1">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                No active students found matching program, batch, semester, and section for this offering.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(count($students) > 0)
        <!-- Form Submit Bar -->
        <div class="flex items-center justify-end space-x-3">
            <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition">
                <i class="fa-solid fa-floppy-disk me-1.5"></i> Save & Submit Attendance
            </button>
        </div>
        @endif
    </form>

</div>

<script>
    function attendanceSheet() {
        return {
            statuses: {
                @foreach($students as $s)
                    {{ $s->id }}: 'Present',
                @endforeach
            },
            markAll(status) {
                for (let key in this.statuses) {
                    this.statuses[key] = status;
                }
            }
        }
    }
</script>
@endsection
