@extends('layouts.app')

@section('title', 'Course Offerings')
@section('header_title', 'Academic Course Offerings')

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false }">

    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Academic Course Offerings</h1>
            <p class="text-xs text-slate-500 mt-1">
                Bridge courses to specific teachers, programs, semesters, batches, and academic sessions.
            </p>
        </div>
        <button @click="createModalOpen = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-md transition flex items-center space-x-2">
            <i class="fa-solid fa-plus"></i>
            <span>Create Course Offering</span>
        </button>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('course-offerings.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Academic Session</label>
                <select name="academic_session_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Sessions</option>
                    @foreach($academicSessions as $session)
                        <option value="{{ $session->id }}" {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Program</label>
                <select name="program_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                            {{ $prog->code }} - {{ $prog->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Teacher</label>
                <select name="teacher_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Master Course</label>
                <select name="course_id" class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $crs)
                        <option value="{{ $crs->id }}" {{ request('course_id') == $crs->id ? 'selected' : '' }}>
                            {{ $crs->course_code }} - {{ $crs->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl text-xs transition">
                    Filter
                </button>
                <a href="{{ route('course-offerings.index') }}" class="px-3 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-semibold text-xs hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Course Offerings Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Master Course</th>
                        <th class="py-3.5 px-4">Assigned Teacher</th>
                        <th class="py-3.5 px-4">Program & Batch</th>
                        <th class="py-3.5 px-4">Sem & Sec</th>
                        <th class="py-3.5 px-4">Academic Session</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($offerings as $offering)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-900">
                            {{ $offering->course->course_code ?? 'N/A' }}
                            <div class="text-[11px] font-normal text-slate-500">{{ $offering->course->title ?? '' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-800">{{ $offering->teacher->name ?? 'Unassigned' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $offering->teacher->email ?? '' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-emerald-700">{{ $offering->program->code ?? 'N/A' }}</span>
                            <div class="text-[10px] text-slate-500">Batch: {{ $offering->batch->name ?? 'Default' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            Sem {{ $offering->semester_number }}
                            @if($offering->section)
                                <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded-md font-semibold text-[10px] ms-1">{{ $offering->section->name }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-600">
                            {{ $offering->academicSession->name ?? 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if($offering->status === 'active')
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold text-[10px] rounded-full">Active</span>
                            @else
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold text-[10px] rounded-full">{{ ucfirst($offering->status) }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <a href="{{ route('attendance.mark.form', $offering->id) }}" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-lg hover:bg-emerald-100 transition" title="Take Attendance">
                                <i class="fa-solid fa-clipboard-user me-1"></i> Attendance
                            </a>
                            <form action="{{ route('course-offerings.destroy', $offering->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this course offering?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">
                            No Course Offerings found matching your query.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($offerings->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $offerings->links() }}
            </div>
        @endif
    </div>

    <!-- Create Course Offering Modal -->
    <div x-show="createModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div @click.away="createModalOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-6 border border-slate-100">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="font-bold text-base text-slate-800">New Course Offering</h3>
                <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('course-offerings.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Master Course *</label>
                        <select name="course_id" required class="w-full rounded-xl border-slate-200 focus:ring-emerald-500">
                            @foreach($courses as $crs)
                                <option value="{{ $crs->id }}">{{ $crs->course_code }} - {{ $crs->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Assigned Teacher *</label>
                        <select name="teacher_id" required class="w-full rounded-xl border-slate-200 focus:ring-emerald-500">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Academic Program *</label>
                        <select name="program_id" required class="w-full rounded-xl border-slate-200 focus:ring-emerald-500">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Academic Batch (Optional)</label>
                        <select name="batch_id" class="w-full rounded-xl border-slate-200 focus:ring-emerald-500">
                            <option value="">Default Cohort (Auto-Assign)</option>
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Semester Number *</label>
                        <input type="number" name="semester_number" value="1" min="1" max="12" required class="w-full rounded-xl border-slate-200 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Section (Optional)</label>
                        <select name="section_id" class="w-full rounded-xl border-slate-200 focus:ring-emerald-500">
                            <option value="">No Section (Entire Batch)</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Academic Session *</label>
                        <select name="academic_session_id" required class="w-full rounded-xl border-slate-200 focus:ring-emerald-500">
                            @foreach($academicSessions as $as)
                                <option value="{{ $as->id }}" {{ $as->is_current ? 'selected' : '' }}>{{ $as->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Offering Status *</label>
                        <select name="status" required class="w-full rounded-xl border-slate-200 focus:ring-emerald-500">
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-xl hover:bg-slate-200 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-500 shadow-md transition">
                        Save Course Offering
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
