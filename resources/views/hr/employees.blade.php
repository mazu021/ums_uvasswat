@extends('layouts.app')

@section('title', 'Employee Directory')
@section('header_title', 'HR Employee & Faculty Directory')

@section('content')
<div class="space-y-6" x-data="{ 
    createModal: false, 
    editModal: false, 
    editEmp: { id: '', first_name: '', last_name: '', employee_code: '', email: '', department_id: '', designation: '', type: '', basic_salary: '', status: '' } 
}">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Faculty & Staff Members</h3>
            <p class="text-xs text-slate-500">Manage employee code, designations, department mapping, and salary structures.</p>
        </div>
        <button @click="createModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2">
            <i class="fa-solid fa-user-plus"></i>
            <span>Add Employee Profile</span>
        </button>
    </div>

    <!-- Employee Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Designation</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Basic Salary</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $emp->employee_code }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $emp->full_name }}
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $emp->email }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">{{ $emp->department->name }}</td>
                            <td class="px-6 py-4 font-semibold text-emerald-800">{{ $emp->designation }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $emp->type === 'faculty' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $emp->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">Rs. {{ number_format($emp->basic_salary, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ ucfirst($emp->status) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5">
                                <a href="{{ route('hr.employees.show', $emp->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg text-[10px] transition">
                                    <i class="fa-solid fa-eye me-1"></i> Profile
                                </a>
                                <button @click="editEmp = { 
                                    id: '{{ $emp->id }}', 
                                    first_name: '{{ addslashes($emp->first_name) }}', 
                                    last_name: '{{ addslashes($emp->last_name) }}', 
                                    employee_code: '{{ addslashes($emp->employee_code) }}', 
                                    email: '{{ addslashes($emp->email) }}', 
                                    department_id: '{{ $emp->department_id }}', 
                                    designation: '{{ addslashes($emp->designation) }}', 
                                    type: '{{ $emp->type }}', 
                                    basic_salary: '{{ $emp->basic_salary }}', 
                                    status: '{{ $emp->status }}' 
                                }; editModal = true" 
                                class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-[10px] transition">
                                    <i class="fa-solid fa-pen-to-square me-1 text-blue-600"></i> Edit
                                </button>
                                <form action="{{ route('hr.employees.destroy', $emp->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete employee profile {{ addslashes($emp->full_name) }}?')">
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
                            <td colspan="8" class="px-6 py-6 text-center text-slate-400">No employees registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-slate-50">
            {{ $employees->links() }}
        </div>
    </div>

    <!-- Create Employee Modal -->
    <div x-show="createModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden" @click.away="createModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Register New Employee / Faculty</h4>
                <button @click="createModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('hr.employees.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">First Name</label>
                        <input type="text" name="first_name" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Employee Code</label>
                        <input type="text" name="employee_code" placeholder="e.g. EMP-UVAS-005" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Department</label>
                        <select name="department_id" required class="w-full px-3 py-2 border rounded-lg">
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Designation</label>
                        <input type="text" name="designation" placeholder="e.g. Assistant Professor" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Staff Type</label>
                        <select name="type" class="w-full px-3 py-2 border rounded-lg">
                            <option value="faculty">Faculty</option>
                            <option value="staff">Staff</option>
                            <option value="administration">Administration</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Basic Salary (PKR)</label>
                        <input type="number" step="0.01" name="basic_salary" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg">
                            <option value="active">Active</option>
                            <option value="on_leave">On Leave</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end space-x-2 border-t">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg shadow">Register Employee</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Edit Employee / Faculty Profile</h4>
                <button @click="editModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form :action="'{{ url('/hr/employees') }}/' + editEmp.id" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">First Name</label>
                        <input type="text" name="first_name" x-model="editEmp.first_name" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" x-model="editEmp.last_name" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Employee Code</label>
                        <input type="text" name="employee_code" x-model="editEmp.employee_code" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" x-model="editEmp.email" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Department</label>
                        <select name="department_id" x-model="editEmp.department_id" required class="w-full px-3 py-2 border rounded-lg">
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Designation</label>
                        <input type="text" name="designation" x-model="editEmp.designation" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Staff Type</label>
                        <select name="type" x-model="editEmp.type" class="w-full px-3 py-2 border rounded-lg">
                            <option value="faculty">Faculty</option>
                            <option value="staff">Staff</option>
                            <option value="administration">Administration</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Basic Salary (PKR)</label>
                        <input type="number" step="0.01" name="basic_salary" x-model="editEmp.basic_salary" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" x-model="editEmp.status" class="w-full px-3 py-2 border rounded-lg">
                            <option value="active">Active</option>
                            <option value="on_leave">On Leave</option>
                            <option value="suspended">Suspended</option>
                            <option value="resigned">Resigned</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end space-x-2 border-t">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow">Update Employee Profile</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
