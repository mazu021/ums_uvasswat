@extends('layouts.app')

@section('title', 'Course Catalog')
@section('header_title', 'Course Catalog & Faculty Assignments')

@section('content')
<div class="space-y-6" x-data="{ courseModal: false, editModal: false, assignModal: false, selectedCourseId: null, editCourse: {} }">

    <!-- Header & Action Bar -->
    <div class="bg-gradient-to-r from-slate-900 via-navy-900 to-slate-800 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between">
        <div>
            <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-semibold rounded-full mb-2 border border-emerald-500/30">
                <i class="fa-solid fa-database me-1"></i> Central University Master Catalog
            </span>
            <h1 class="text-2xl font-bold tracking-tight">Master Courses Catalog</h1>
            <p class="text-slate-300 text-xs mt-1">
                Single-source-of-truth course definitions mapped across program Study Schemes (Curriculums) without master data duplication.
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <a href="{{ route('academics.curriculum.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-medium text-xs rounded-xl border border-slate-700 shadow transition flex items-center">
                <i class="fa-solid fa-graduation-cap me-2 text-emerald-400"></i> View Study Schemes
            </a>
            <button @click="courseModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Add Master Course</span>
            </button>
        </div>
    </div>

    <!-- Courses Table (Fits in single view without horizontal scroll) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left text-slate-600">
            <thead class="bg-slate-50/90 text-slate-700 font-bold uppercase border-b border-slate-200 text-[10px] tracking-wider">
                <tr>
                    <th class="px-3.5 py-3">Master Code</th>
                    <th class="px-3.5 py-3">Course Title</th>
                    <th class="px-3.5 py-3">Course Dept</th>
                    <th class="px-2 py-3 text-center">Credits</th>
                    <th class="px-3.5 py-3">Mapped Schemes</th>
                    <th class="px-3.5 py-3">Instructors</th>
                    <th class="px-3.5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($courses as $c)
                    <tr class="hover:bg-slate-50/80 transition">
                        <!-- Master Code -->
                        <td class="px-3.5 py-3 whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200/90 rounded-lg font-mono font-bold text-xs">
                                {{ $c->course_code }}
                            </span>
                        </td>

                        <!-- Course Title -->
                        <td class="px-3.5 py-3">
                            <span class="font-bold text-slate-900 text-xs block leading-tight">{{ $c->title }}</span>
                            @if($c->description)
                                <span class="text-[10px] text-slate-400 block truncate max-w-[200px] mt-0.5">{{ $c->description }}</span>
                            @endif
                        </td>

                        <!-- Department -->
                        <td class="px-3.5 py-3 text-slate-700 text-[11px] whitespace-nowrap">
                            {{ $c->department->name ?? 'General / Shared' }}
                        </td>

                        <!-- Credit Hours -->
                        <td class="px-2 py-3 text-center whitespace-nowrap">
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded font-bold text-[11px]">{{ $c->credit_hours }} Cr</span>
                        </td>

                        <!-- Mapped Curriculums -->
                        <td class="px-3.5 py-3">
                            @forelse($c->curriculumCourses as $cc)
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-900 border border-blue-200/80 font-semibold rounded text-[10px] me-1 mb-0.5 whitespace-nowrap shadow-2xs">
                                    <i class="fa-solid fa-graduation-cap text-blue-600"></i>
                                    <span>{{ $cc->curriculum->program->code ?? $cc->curriculum->name }} (S{{ $cc->semester_number }})</span>
                                </div>
                            @empty
                                <span class="text-slate-400 italic text-[10px]">Unmapped</span>
                            @endforelse
                        </td>

                        <!-- Assigned Instructors -->
                        <td class="px-3.5 py-3">
                            @forelse($c->getAssignedTeachersWithDetails() as $t)
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-800 font-bold rounded text-[10px] me-1 mb-0.5 whitespace-nowrap shadow-2xs">
                                    <span>{{ $t['name'] }}</span>
                                    <form action="{{ route('academics.courses.unassign') }}" method="POST" class="inline" onsubmit="return confirm('Unassign {{ $t['name'] }} from {{ $c->course_code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="course_id" value="{{ $c->id }}">
                                        @if(!empty($t['offering_id']))
                                            <input type="hidden" name="offering_id" value="{{ $t['offering_id'] }}">
                                        @endif
                                        @if($t['user_id'])
                                            <input type="hidden" name="user_id" value="{{ $t['user_id'] }}">
                                        @endif
                                        @if($t['employee_id'])
                                            <input type="hidden" name="employee_id" value="{{ $t['employee_id'] }}">
                                        @endif
                                        <button type="submit" class="text-purple-600 hover:text-red-600 transition font-bold px-0.5" title="Unassign Instructor">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <span class="text-slate-400 italic text-[10px]">Unassigned</span>
                            @endforelse
                        </td>

                        <!-- Actions -->
                        <td class="px-3.5 py-3 text-right whitespace-nowrap">
                            <div class="inline-flex items-center space-x-1">
                                <button @click="selectedCourseId = {{ $c->id }}; assignModal = true" class="px-2.5 py-1 bg-navy-900 hover:bg-slate-800 text-white font-bold rounded-lg text-[10px] transition" title="Assign Instructor">
                                    <i class="fa-solid fa-user-plus me-1"></i> Assign
                                </button>
                                
                                <button @click="editCourse = { 
                                    id: {{ $c->id }}, 
                                    course_code: '{{ addslashes($c->course_code) }}', 
                                    title: '{{ addslashes($c->title) }}', 
                                    department_id: {{ $c->department_id ?? 'null' }}, 
                                    credit_hours: {{ $c->credit_hours }}, 
                                    semester: {{ $c->semester ?? 1 }}, 
                                    description: '{{ addslashes($c->description ?? '') }}' 
                                }; editModal = true" class="p-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold rounded-lg text-xs transition" title="Edit Course">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <form action="{{ route('academics.courses.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete course {{ $c->course_code }}? This will remove all assigned offerings.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold rounded-lg text-xs transition" title="Delete Course">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                            <i class="fa-solid fa-folder-open text-3xl text-slate-300 mb-2 block"></i>
                            No courses found in the catalog.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $courses->links() }}
        </div>
    </div>

    <!-- Create Course Modal -->
    <div x-show="courseModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="courseModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm"><i class="fa-solid fa-plus me-1.5 text-emerald-400"></i> Add Course to Catalog</h4>
                <button @click="courseModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('academics.courses.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Department</label>
                    <select name="department_id" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Course Code</label>
                        <input type="text" name="course_code" placeholder="e.g. VET-401" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Credit Hours</label>
                        <input type="number" name="credit_hours" value="3" min="1" max="6" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Course Title</label>
                    <input type="text" name="title" placeholder="e.g. Veterinary Clinical Medicine" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Semester</label>
                    <input type="number" name="semester" value="1" min="1" max="10" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="2" placeholder="Course outline details..." class="w-full px-3 py-2 border border-slate-200 rounded-lg"></textarea>
                </div>
                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="courseModal = false" class="px-3.5 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow">Save Course</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm"><i class="fa-solid fa-pen-to-square me-1.5 text-blue-400"></i> Edit Course Catalog Entry</h4>
                <button @click="editModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form :action="'{{ url('/academics/courses') }}/' + editCourse.id" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Department</label>
                    <select name="department_id" x-model="editCourse.department_id" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Course Code</label>
                        <input type="text" name="course_code" x-model="editCourse.course_code" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Credit Hours</label>
                        <input type="number" name="credit_hours" x-model="editCourse.credit_hours" min="1" max="6" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Course Title</label>
                    <input type="text" name="title" x-model="editCourse.title" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Semester</label>
                    <input type="number" name="semester" x-model="editCourse.semester" min="1" max="10" required class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" x-model="editCourse.description" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg"></textarea>
                </div>
                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-3.5 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow">Update Course</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign Instructor Modal -->
    <div x-show="assignModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" x-data="{ assignDeptId: '', assignProgId: '' }">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden" @click.away="assignModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-user-plus text-emerald-400"></i> Assign Instructor to Course Offering
                    </h4>
                    <p class="text-[11px] text-slate-300 font-normal mt-0.5">Creates a Course Offering (CRN) linking Master Course + Teacher + Program + Batch.</p>
                </div>
                <button @click="assignModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('academics.courses.assign') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <input type="hidden" name="course_id" :value="selectedCourseId">
                
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select Faculty Instructor (Teacher) *</label>
                    <select name="employee_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-emerald-500">
                        @foreach($faculties as $f)
                            <option value="{{ $f->id }}">{{ $f->full_name }} ({{ $f->designation }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Filter Department</label>
                        <select x-model="assignDeptId" class="w-full px-3 py-2 border border-slate-200 rounded-xl">
                            <option value="">All Departments</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Target Degree Program *</label>
                        <select name="program_id" x-model="assignProgId" required class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-emerald-500">
                            <option value="">Select Program</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}" x-show="!assignDeptId || assignDeptId == '{{ $prog->department_id }}'">
                                    {{ $prog->name }} ({{ $prog->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Academic Batch (Optional)</label>
                        <select name="batch_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-emerald-500">
                            <option value="">Default Cohort (Auto-Assign)</option>
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}" x-show="!assignProgId || assignProgId == '{{ $b->program_id }}'">
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Academic Session *</label>
                        <select name="academic_session_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-emerald-500">
                            @foreach($academicSessions as $session)
                                <option value="{{ $session->id }}" {{ $session->is_current ? 'selected' : '' }}>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Semester Number *</label>
                        <input type="number" name="semester" value="1" min="1" max="12" required class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Section (Optional)</label>
                        <select name="section_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-emerald-500">
                            <option value="">Entire Batch (No Section)</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="assignModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow transition">Create Offering & Assign</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
