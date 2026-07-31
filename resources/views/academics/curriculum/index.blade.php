@extends('layouts.app')

@section('title', 'Curriculum & Study Schemes')
@section('header_title', 'Curriculum Management (Study Scheme)')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-gradient-to-r from-slate-900 via-navy-900 to-slate-800 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between">
        <div>
            <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-semibold rounded-full mb-2 border border-emerald-500/30">
                <i class="fa-solid fa-graduation-cap me-1"></i> Academic Degree Planning
            </span>
            <h1 class="text-2xl font-bold tracking-tight">Curriculum & Study Schemes</h1>
            <p class="text-slate-300 text-xs mt-1">
                Define degree program course maps per semester. Master courses are assigned to program schemes without duplication.
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <button onclick="document.getElementById('createCurriculumModal').classList.remove('hidden')" 
                    class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs rounded-xl shadow-lg transition flex items-center">
                <i class="fa-solid fa-plus me-2"></i> Create Study Scheme
            </button>
        </div>
    </div>



    <!-- Program Selection Tabs & Scheme Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <form method="GET" action="{{ route('academics.curriculum.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[240px]">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Select Academic Program</label>
                <select name="program_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ $selectedProgramId == $prog->id ? 'selected' : '' }}>
                            {{ $prog->name }} ({{ $prog->code ?? 'N/A' }}) - {{ $prog->department->name ?? 'Faculty' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if($activeCurriculum)
            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $activeCurriculum->name }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Code: <span class="font-mono font-bold text-slate-700 me-3">{{ $activeCurriculum->code ?? 'N/A' }}</span>
                        Effective Year: <span class="font-bold text-slate-700 me-3">{{ $activeCurriculum->effective_year }}</span>
                        Total Semesters: <span class="font-bold text-slate-700 me-3">{{ $activeCurriculum->total_semesters }}</span>
                        Target Credits: <span class="font-bold text-emerald-700 me-3">{{ $activeCurriculum->total_credit_hours }} Cr</span>
                    </p>
                </div>
                <div>
                    <button onclick="document.getElementById('addCourseModal').classList.remove('hidden')"
                            class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs rounded-xl shadow transition">
                        <i class="fa-solid fa-book-bookmark me-1.5"></i> Add Master Course to Scheme
                    </button>
                </div>
            </div>
        @else
            <div class="py-8 text-center text-slate-500">
                <i class="fa-solid fa-folder-open text-4xl text-slate-300 mb-2"></i>
                <p class="text-sm font-medium">No Study Scheme defined for this program yet.</p>
                <p class="text-xs text-slate-400 mt-1">Click "Create Study Scheme" above to initialize the curriculum.</p>
            </div>
        @endif
    </div>

    <!-- Curriculum Semesters Grid -->
    @if($activeCurriculum)
        <div class="space-y-6">
            @for($sem = 1; $sem <= $activeCurriculum->total_semesters; $sem++)
                @php
                    $semCourses = $activeCurriculum->coursesForSemester($sem);
                    $semCredits = $semCourses->sum('credit_hours');
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-lg bg-navy-900 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                S{{ $sem }}
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Semester {{ $sem }}</h3>
                                <p class="text-[11px] text-slate-500">{{ $semCourses->count() }} Courses Assigned</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">
                            {{ $semCredits }} Credit Hours
                        </span>
                    </div>

                    <div class="p-5">
                        @if($semCourses->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                            <th class="pb-2">Course Code</th>
                                            <th class="pb-2">Title</th>
                                            <th class="pb-2">Course Type</th>
                                            <th class="pb-2 text-center">Credit Hours</th>
                                            <th class="pb-2 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @foreach($semCourses as $curCourse)
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <td class="py-3 font-mono font-bold text-slate-800">
                                                    {{ $curCourse->course->course_code }}
                                                </td>
                                                <td class="py-3 font-semibold text-slate-900">
                                                    {{ $curCourse->course->title }}
                                                </td>
                                                <td class="py-3">
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize 
                                                        {{ $curCourse->course_type === 'core' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $curCourse->course_type === 'lab' ? 'bg-purple-100 text-purple-800' : '' }}
                                                        {{ $curCourse->course_type === 'elective' ? 'bg-amber-100 text-amber-800' : '' }}
                                                        {{ $curCourse->course_type === 'general' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                        {{ $curCourse->course_type === 'project' ? 'bg-rose-100 text-rose-800' : '' }}">
                                                        {{ $curCourse->course_type }}
                                                    </span>
                                                </td>
                                                <td class="py-3 text-center font-bold text-slate-800">
                                                    {{ $curCourse->credit_hours }} Cr
                                                </td>
                                                <td class="py-3 text-right">
                                                    <form method="POST" action="{{ route('academics.curriculum.remove-course', $curCourse->id) }}" onsubmit="return confirm('Remove this course from Semester {{ $sem }} study scheme?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-rose-600 hover:text-rose-800 p-1 font-semibold text-xs transition">
                                                            <i class="fa-solid fa-trash-can me-1"></i> Remove
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-4 text-center text-slate-400 text-xs italic">
                                No courses assigned to Semester {{ $sem }} yet.
                            </div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    @endif

</div>

<!-- Create Curriculum Modal -->
<div id="createCurriculumModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-slate-900">Create Study Scheme</h3>
            <button onclick="document.getElementById('createCurriculumModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('academics.curriculum.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Target Program</label>
                <select name="program_id" required class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}">{{ $prog->name }} ({{ $prog->code ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Curriculum / Scheme Title</label>
                <input type="text" name="name" required placeholder="e.g. Doctor of Physical Therapy Scheme 2026-2031" class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Scheme Code</label>
                    <input type="text" name="code" placeholder="e.g. SCHEME-DPT-2026" class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Effective Year</label>
                    <input type="number" name="effective_year" value="2026" required class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Total Semesters</label>
                    <input type="number" name="total_semesters" value="10" required class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Total Credit Hours</label>
                    <input type="number" name="total_credit_hours" value="135" required class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
                    <option value="active">Active</option>
                    <option value="draft">Draft</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="document.getElementById('createCurriculumModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow">Save Scheme</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Course to Curriculum Modal -->
@if($activeCurriculum)
<div id="addCourseModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-slate-900">Add Master Course to Curriculum</h3>
            <button onclick="document.getElementById('addCourseModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('academics.curriculum.add-course', $activeCurriculum->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Select Master Course</label>
                <select name="course_id" required class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
                    @foreach($courses as $crs)
                        <option value="{{ $crs->id }}">
                            {{ $crs->course_code }} - {{ $crs->title }} ({{ $crs->credit_hours }} Cr)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Target Semester</label>
                    <select name="semester_number" required class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
                        @for($s = 1; $s <= $activeCurriculum->total_semesters; $s++)
                            <option value="{{ $s }}">Semester {{ $s }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Course Type</label>
                    <select name="course_type" required class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
                        <option value="core">Core</option>
                        <option value="elective">Elective</option>
                        <option value="general">General</option>
                        <option value="lab">Lab</option>
                        <option value="project">Project</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Credit Hours</label>
                <input type="number" name="credit_hours" value="3" required min="1" max="10" class="w-full bg-slate-50 border text-xs rounded-xl p-2.5">
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="document.getElementById('addCourseModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow">Map to Curriculum</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
