@extends('layouts.app')

@section('title', 'Faculties & Departments')
@section('header_title', 'Faculties & Academic Departments')

@section('content')
<div class="space-y-6" x-data="{ 
    facultyModal: false, 
    editFacultyModal: false, 
    deptModal: false, 
    editDeptModal: false, 
    editFaculty: { id: '', name: '', code: '', dean_name: '', description: '' },
    editDept: { id: '', faculty_id: '', name: '', code: '', hod_name: '', description: '' }
}">

    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-800">UVAS Swat Faculties & Offices</h3>
            <p class="text-xs text-slate-500">Manage university faculties, clinical departments, and administrative offices.</p>
        </div>
        <div class="flex space-x-2">
            <button @click="facultyModal = true" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow">
                <i class="fa-solid fa-building-columns me-1"></i> Add Faculty
            </button>
            <button @click="deptModal = true" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow">
                <i class="fa-solid fa-plus me-1"></i> Add Department
            </button>
        </div>
    </div>

    <!-- Faculties Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($faculties as $f)
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3 relative group">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-lg">{{ $f->code }}</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-slate-400 font-medium me-2">{{ $f->departments->count() }} Departments</span>
                        <!-- Edit & Delete Faculty Action Buttons -->
                        <button @click="editFaculty = { id: '{{ $f->id }}', name: '{{ addslashes($f->name) }}', code: '{{ addslashes($f->code) }}', dean_name: '{{ addslashes($f->dean_name) }}', description: '{{ addslashes($f->description) }}' }; editFacultyModal = true" 
                                title="Edit Faculty" class="p-1 text-slate-400 hover:text-blue-600 transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form action="{{ route('hr.faculties.destroy', $f->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($f->name) }} and all associated departments?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete Faculty" class="p-1 text-slate-400 hover:text-red-600 transition">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <h4 class="font-bold text-base text-slate-900">{{ $f->name }}</h4>
                <p class="text-xs text-slate-500 line-clamp-2">{{ $f->description }}</p>
                <div class="pt-3 border-t border-slate-100 text-xs text-slate-600 flex items-center justify-between">
                    <span><i class="fa-solid fa-user-shield me-1 text-slate-400"></i> Dean:</span>
                    <span class="font-bold text-slate-800">{{ $f->dean_name ?? 'Unassigned' }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Departments Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-slate-50 font-bold text-slate-800 text-sm">
            All Registered Academic & Clinical Departments
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Department Name</th>
                        <th class="px-6 py-3">Parent Faculty</th>
                        <th class="px-6 py-3">Head of Dept (HOD)</th>
                        <th class="px-6 py-3">Employees</th>
                        <th class="px-6 py-3">Enrolled Students</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($departments as $d)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-emerald-700">{{ $d->code }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $d->name }}</td>
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $d->faculty->name }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $d->hod_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $d->employees->count() }} Staff</td>
                            <td class="px-6 py-4 font-bold text-emerald-600">{{ $d->students->count() }} Students</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="editDept = { id: '{{ $d->id }}', faculty_id: '{{ $d->faculty_id }}', name: '{{ addslashes($d->name) }}', code: '{{ addslashes($d->code) }}', hod_name: '{{ addslashes($d->hod_name) }}', description: '{{ addslashes($d->description) }}' }; editDeptModal = true" 
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-[10px] transition">
                                    <i class="fa-solid fa-pen-to-square me-1 text-blue-600"></i> Edit
                                </button>
                                <form action="{{ route('hr.departments.destroy', $d->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete the department {{ addslashes($d->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-lg text-[10px] transition">
                                        <i class="fa-solid fa-trash-can me-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-6 text-center text-slate-400">No departments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-slate-50">
            {{ $departments->links() }}
        </div>
    </div>

    <!-- Create Faculty Modal -->
    <div x-show="facultyModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="facultyModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Add New Faculty</h4>
                <button @click="facultyModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('hr.faculties.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Faculty Name</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Faculty Code</label>
                    <input type="text" name="code" placeholder="e.g. FVS" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Dean Name</label>
                    <input type="text" name="dean_name" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="facultyModal = false" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white font-bold rounded-lg shadow">Save Faculty</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Faculty Modal -->
    <div x-show="editFacultyModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="editFacultyModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Edit Faculty Details</h4>
                <button @click="editFacultyModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form :action="'{{ url('/hr/faculties') }}/' + editFaculty.id" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Faculty Name</label>
                    <input type="text" name="name" x-model="editFaculty.name" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Faculty Code</label>
                    <input type="text" name="code" x-model="editFaculty.code" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Dean Name</label>
                    <input type="text" name="dean_name" x-model="editFaculty.dean_name" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" x-model="editFaculty.description" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="editFacultyModal = false" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow">Update Faculty</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Department Modal -->
    <div x-show="deptModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="deptModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Add New Department</h4>
                <button @click="deptModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('hr.departments.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Parent Faculty</label>
                    <select name="faculty_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach($faculties as $f)
                            <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Department Name</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Department Code</label>
                    <input type="text" name="code" placeholder="e.g. DVMS" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Head of Department (HOD)</label>
                    <input type="text" name="hod_name" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="deptModal = false" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white font-bold rounded-lg shadow">Save Department</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div x-show="editDeptModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="editDeptModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Edit Department Details</h4>
                <button @click="editDeptModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form :action="'{{ url('/hr/departments') }}/' + editDept.id" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Parent Faculty</label>
                    <select name="faculty_id" x-model="editDept.faculty_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach($faculties as $f)
                            <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Department Name</label>
                    <input type="text" name="name" x-model="editDept.name" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Department Code</label>
                    <input type="text" name="code" x-model="editDept.code" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Head of Department (HOD)</label>
                    <input type="text" name="hod_name" x-model="editDept.hod_name" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="editDeptModal = false" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow">Update Department</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
