@extends('layouts.app')

@section('title', 'Student Registry')
@section('header_title', 'Student Admissions & Enrollment Registry')

@section('content')
<div class="space-y-6" x-data="{ 
    enrollModal: false, 
    importModal: false, 
    editModal: false, 
    editStudent: {},
    promoteModal: false,
    promoteDeptId: '',
    promoteCurrentSem: '1',
    promoteTargetSem: 2,
    promoteStudentsList: [],
    selectedStudentsMap: {},
    loadingStudents: false,
    fetchPromoteStudents() {
        this.loadingStudents = true;
        this.promoteStudentsList = [];
        this.selectedStudentsMap = {};
        this.promoteTargetSem = parseInt(this.promoteCurrentSem) + 1;
        fetch(`{{ route('academics.students.batch-list') }}?department_id=${this.promoteDeptId}&current_semester=${this.promoteCurrentSem}`)
            .then(res => res.json())
            .then(data => {
                this.promoteStudentsList = data.students || [];
                this.promoteStudentsList.forEach(s => { this.selectedStudentsMap[s.id] = true; });
                this.loadingStudents = false;
            })
            .catch(() => { this.loadingStudents = false; });
    },
    toggleAll(checked) {
        this.promoteStudentsList.forEach(s => { this.selectedStudentsMap[s.id] = checked; });
    }
}">

    @if($errors->has('excel_file'))
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl text-rose-700 text-xs font-semibold flex items-center space-x-2 shadow-sm">
            <i class="fa-solid fa-circle-exclamation text-base"></i>
            <span>{{ $errors->first('excel_file') }}</span>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Student Registry & Admissions</h3>
            <p class="text-xs text-slate-500">Manage student profiles, registration numbers, departments, and active semesters.</p>
        </div>
        <div class="flex items-center space-x-2">
            <button @click="promoteModal = true; fetchPromoteStudents()" class="px-4 py-2.5 bg-amber-400 hover:bg-amber-500 text-slate-900 font-extrabold text-xs rounded-xl shadow flex items-center space-x-2 transition">
                <i class="fa-solid fa-arrow-up-right-dots"></i>
                <span>Promote Batch</span>
            </button>
            <button @click="importModal = true" class="px-4 py-2.5 bg-[#2e2e7f] hover:bg-[#232363] text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
                <i class="fa-solid fa-file-excel text-amber-400"></i>
                <span>Upload Excel Sheet</span>
            </button>
            <button @click="enrollModal = true" class="px-4 py-2.5 bg-[#00a257] hover:bg-[#008246] text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition">
                <i class="fa-solid fa-user-graduate"></i>
                <span>New Student Admission</span>
            </button>
        </div>
    </div>

    <!-- Table Bar Filter -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('academics.students.index') }}" class="w-full sm:w-auto flex items-center space-x-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Reg No, Roll No or Name..." class="px-3 py-1.5 border rounded-lg text-xs w-64">
            <select name="department_id" onchange="this.form.submit()" class="px-3 py-1.5 border rounded-lg text-xs font-semibold text-slate-700">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ $d->id == $departmentId ? 'selected' : '' }}>{{ $d->code }} - {{ $d->name }}</option>
                @endforeach
            </select>
            <select name="semester" onchange="this.form.submit()" class="px-3 py-1.5 border rounded-lg text-xs font-semibold text-slate-700">
                <option value="">All Semesters</option>
                @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ $semesterFilter == $i ? 'selected' : '' }}>Sem {{ $i }}</option>
                @endfor
            </select>
            <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 border rounded-lg text-xs font-bold text-slate-900 bg-slate-50">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Per Page</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Per Page</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Per Page</option>
                <option value="100" {{ request('per_page', 100) == 100 ? 'selected' : '' }}>100 Per Page (Default)</option>
                <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250 Per Page</option>
                <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500 Per Page</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-slate-900 text-white font-bold text-xs rounded-lg shadow">Filter</button>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Reg No.</th>
                        <th class="px-6 py-3">Roll No.</th>
                        <th class="px-6 py-3">Student Name</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Semester</th>
                        <th class="px-6 py-3">Gender</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $std)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-emerald-700">{{ $std->registration_number }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $std->roll_number }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $std->full_name }}
                                <span class="block text-[10px] text-slate-400 font-normal">Father: {{ $std->father_name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $std->department->name }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">Sem {{ $std->current_semester }}</td>
                            <td class="px-6 py-4 uppercase text-slate-500">{{ $std->gender }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <!-- VIEW BUTTON -->
                                    <a href="{{ route('academics.students.show', $std->id) }}" 
                                       title="View Profile & Transcript" 
                                       class="px-2.5 py-1.5 bg-[#2e2e7f]/10 hover:bg-[#2e2e7f] text-[#2e2e7f] hover:text-white font-bold rounded-lg text-[11px] transition flex items-center space-x-1">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>View</span>
                                    </a>

                                    <!-- EDIT BUTTON -->
                                    <button type="button" 
                                            @click="editStudent = {{ json_encode([
                                                'id' => $std->id,
                                                'first_name' => $std->first_name,
                                                'last_name' => $std->last_name,
                                                'father_name' => $std->father_name,
                                                'registration_number' => $std->registration_number,
                                                'roll_number' => $std->roll_number,
                                                'email' => $std->email,
                                                'phone' => $std->phone,
                                                'cnic' => $std->cnic,
                                                'department_id' => $std->department_id,
                                                'current_semester' => $std->current_semester,
                                                'gender' => $std->gender,
                                                'status' => $std->status ?? 'active',
                                                'update_url' => route('academics.students.update', $std->id)
                                            ]) }}; editModal = true" 
                                            title="Edit Student Profile" 
                                            class="px-2.5 py-1.5 bg-amber-100 hover:bg-amber-500 text-amber-900 hover:text-white font-bold rounded-lg text-[11px] transition flex items-center space-x-1">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        <span>Edit</span>
                                    </button>

                                    <!-- DELETE BUTTON -->
                                    <form action="{{ route('academics.students.destroy', $std->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete student {{ $std->full_name }} ({{ $std->registration_number }})? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Delete Student" 
                                                class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-600 text-rose-700 hover:text-white font-bold rounded-lg text-[11px] transition flex items-center space-x-1">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-6 text-center text-slate-400">No students enrolled.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-slate-50">
            {{ $students->links() }}
        </div>
    </div>

    <!-- Enroll Student Modal -->
    <div x-show="enrollModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden" @click.away="enrollModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">New Student Admission Form</h4>
                <button @click="enrollModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('academics.students.store') }}" method="POST" class="p-6 space-y-4 text-xs">
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
                        <label class="block font-bold text-slate-700 mb-1">Registration Number</label>
                        <input type="text" name="registration_number" placeholder="e.g. 2026-UVAS-DVM-010" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Roll Number</label>
                        <input type="text" name="roll_number" placeholder="e.g. DVM-26-10" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Father's Name</label>
                        <input type="text" name="father_name" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Department</label>
                        <select name="department_id" required class="w-full px-3 py-2 border rounded-lg">
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Current Semester</label>
                        <input type="number" name="current_semester" value="1" min="1" max="10" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Gender</label>
                        <select name="gender" class="w-full px-3 py-2 border rounded-lg">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="hidden">
                    <input type="hidden" name="status" value="active">
                </div>
                <div class="pt-4 flex justify-end space-x-2 border-t">
                    <button type="button" @click="enrollModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg shadow">Complete Admission</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Excel Sheet Modal -->
    <div x-show="importModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden" @click.away="importModal = false">
            <div class="px-6 py-4 bg-[#2e2e7f] text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-file-excel text-amber-400 text-lg"></i>
                    <h4 class="font-extrabold text-sm">Bulk Import Students from Excel Sheet</h4>
                </div>
                <button @click="importModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form action="{{ route('academics.students.import-excel') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5 text-xs">
                @csrf
                
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-[#2e2e7f] flex items-center"><i class="fa-solid fa-circle-info me-1.5 text-blue-600"></i> Supported Excel / CSV Format</span>
                        <a href="{{ route('academics.students.download-sample') }}" class="px-3 py-1 bg-amber-400 hover:bg-amber-500 text-slate-900 font-extrabold rounded-lg text-[11px] shadow transition flex items-center gap-1.5">
                            <i class="fa-solid fa-download"></i>
                            <span>Download Sample CSV Template</span>
                        </a>
                    </div>
                    <p class="text-slate-600 text-[11px] leading-relaxed">
                        Ensure your Excel sheet or CSV includes the following exact column headers:
                    </p>
                    <div class="flex flex-wrap gap-1 pt-1">
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">S.No</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">Full Name</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">Father Name</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">CNIC</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">Phone No</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">Father Phone</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">Gender</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">DOB</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">Nationality</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">Religion</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">Domicile</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">SSC Total</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">SSC Obtained</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">HSSC Total</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">HSSC Obtained</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-slate-700 font-bold">Merit %</span>
                        <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-mono text-emerald-800 font-bold bg-emerald-50 border-emerald-200">Deparment</span>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-2">Select Excel / CSV File</label>
                    <input type="file" name="excel_file" accept=".csv,.txt,.xlsx,.xls" required 
                           class="w-full px-3.5 py-3 border-2 border-dashed border-slate-300 rounded-xl text-xs bg-slate-50 focus:bg-white focus:border-[#2e2e7f] transition">
                    <p class="text-[10px] text-slate-400 mt-1">Upload .csv or .xlsx containing all department student data.</p>
                </div>

                <div class="pt-4 flex justify-end space-x-2 border-t">
                    <button type="button" @click="importModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#2e2e7f] hover:bg-[#232363] text-white font-extrabold rounded-xl shadow flex items-center space-x-2">
                        <i class="fa-solid fa-[#00a257] fa-cloud-arrow-up text-amber-400"></i>
                        <span>Analyze & Import Excel Sheet</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col" @click.away="editModal = false">
            <div class="px-6 py-4 bg-[#2e2e7f] text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-user-pen text-amber-400 text-lg"></i>
                    <h4 class="font-extrabold text-sm">Edit Student Profile</h4>
                </div>
                <button @click="editModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="editStudent.update_url" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">First Name</label>
                        <input type="text" name="first_name" x-model="editStudent.first_name" required class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" x-model="editStudent.last_name" required class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Registration Number</label>
                        <input type="text" name="registration_number" x-model="editStudent.registration_number" required class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Roll Number</label>
                        <input type="text" name="roll_number" x-model="editStudent.roll_number" required class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Father's Name</label>
                        <input type="text" name="father_name" x-model="editStudent.father_name" class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">CNIC</label>
                        <input type="text" name="cnic" x-model="editStudent.cnic" class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" x-model="editStudent.email" required class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" x-model="editStudent.phone" class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div class="col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Department</label>
                        <select name="department_id" x-model="editStudent.department_id" required class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Semester</label>
                        <input type="number" name="current_semester" x-model="editStudent.current_semester" min="1" max="12" required class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Gender</label>
                        <select name="gender" x-model="editStudent.gender" class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Account Status</label>
                    <select name="status" x-model="editStudent.status" class="w-full px-3 py-2 border rounded-lg focus:border-[#2e2e7f] focus:outline-none font-bold">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="graduated">Graduated</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-extrabold rounded-xl shadow flex items-center space-x-1.5 transition">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Student Changes</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Promote Batch Modal -->
    <div x-show="promoteModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden" @click.away="promoteModal = false">
            <div class="px-6 py-4 bg-gradient-to-r from-[#2e2e7f] to-[#00a257] text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-amber-400 text-slate-900 rounded-lg flex items-center justify-center font-bold">
                        <i class="fa-solid fa-arrow-up-right-dots"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm">Batch Student Promotion Workflow</h4>
                        <p class="text-[11px] text-amber-200">Promote an entire semester batch to the next academic semester</p>
                    </div>
                </div>
                <button @click="promoteModal = false" class="text-white/70 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('academics.students.promote-batch') }}" method="POST" class="p-6 space-y-5 text-xs">
                @csrf

                <!-- Selection Bar: Department & Current Semester -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Target Department</label>
                        <select x-model="promoteDeptId" @change="fetchPromoteStudents()" class="w-full px-3 py-2 border rounded-lg font-semibold text-slate-800 focus:border-[#2e2e7f]">
                            <option value="">All Departments</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Current Semester</label>
                        <select x-model="promoteCurrentSem" @change="fetchPromoteStudents()" class="w-full px-3 py-2 border rounded-lg font-semibold text-slate-800 focus:border-[#2e2e7f]">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-emerald-800 mb-1">Promote To (Target)</label>
                        <select name="target_semester" x-model="promoteTargetSem" class="w-full px-3 py-2 border-2 border-[#00a257] bg-emerald-50 rounded-lg font-extrabold text-[#00a257]">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Student List Review & Hold Back Checklist -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h5 class="font-extrabold text-slate-800">Review Student List (<span x-text="promoteStudentsList.length"></span> Found)</h5>
                            <p class="text-[11px] text-slate-500">Uncheck any failed/held-back students so they stay behind in their current semester.</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="toggleAll(true)" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-md text-[10px]">
                                Select All
                            </button>
                            <button type="button" @click="toggleAll(false)" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-md text-[10px]">
                                Deselect All
                            </button>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden max-h-60 overflow-y-auto">
                        <template x-if="loadingStudents">
                            <div class="p-8 text-center text-slate-400 font-semibold">
                                <i class="fa-solid fa-spinner fa-spin text-xl text-[#2e2e7f] mb-2 block"></i>
                                Loading students for promotion...
                            </div>
                        </template>

                        <template x-if="!loadingStudents && promoteStudentsList.length === 0">
                            <div class="p-8 text-center text-slate-400 font-semibold">
                                <i class="fa-solid fa-user-slash text-xl mb-2 block"></i>
                                No active students found in this semester and department.
                            </div>
                        </template>

                        <template x-if="!loadingStudents && promoteStudentsList.length > 0">
                            <table class="w-full text-xs text-left text-slate-600">
                                <thead class="bg-slate-100 text-slate-700 font-bold uppercase sticky top-0 border-b">
                                    <tr>
                                        <th class="p-3 w-10 text-center">Promote</th>
                                        <th class="p-3">Reg No / Roll No</th>
                                        <th class="p-3">Student Name</th>
                                        <th class="p-3">Department</th>
                                        <th class="p-3 text-right">Status Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="s in promoteStudentsList" :key="s.id">
                                        <tr class="hover:bg-slate-50 transition" :class="{ 'bg-emerald-50/50': selectedStudentsMap[s.id], 'bg-rose-50/40': !selectedStudentsMap[s.id] }">
                                            <td class="p-3 text-center">
                                                <input type="checkbox" name="student_ids[]" :value="s.id" x-model="selectedStudentsMap[s.id]" class="w-4 h-4 text-[#00a257] rounded focus:ring-[#00a257]">
                                            </td>
                                            <td class="p-3 font-bold text-slate-800">
                                                <span x-text="s.registration_number" class="text-emerald-700 block"></span>
                                                <span x-text="s.roll_number" class="text-[10px] text-slate-400 font-normal"></span>
                                            </td>
                                            <td class="p-3 font-extrabold text-slate-900">
                                                <span x-text="s.first_name + ' ' + s.last_name"></span>
                                            </td>
                                            <td class="p-3 font-medium text-slate-600" x-text="s.department ? s.department.name : 'General'"></td>
                                            <td class="p-3 text-right">
                                                <span x-show="selectedStudentsMap[s.id]" class="px-2 py-0.5 bg-emerald-100 text-[#00a257] font-extrabold rounded-full text-[10px]">
                                                    Promote to Sem <span x-text="promoteTargetSem"></span>
                                                </span>
                                                <span x-show="!selectedStudentsMap[s.id]" class="px-2 py-0.5 bg-rose-100 text-rose-700 font-extrabold rounded-full text-[10px]">
                                                    Hold Back (Sem <span x-text="s.current_semester"></span>)
                                                </span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </template>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between border-t">
                    <span class="text-slate-500 font-semibold">
                        Selected to Promote: <strong class="text-[#00a257]" x-text="Object.values(selectedStudentsMap).filter(Boolean).length"></strong> / <span x-text="promoteStudentsList.length"></span>
                    </span>
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="promoteModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                        <button type="submit" :disabled="Object.values(selectedStudentsMap).filter(Boolean).length === 0" 
                                class="px-6 py-2.5 bg-gradient-to-r from-[#2e2e7f] to-[#00a257] text-white font-extrabold rounded-xl shadow transition disabled:opacity-50 flex items-center space-x-2">
                            <i class="fa-solid fa-check"></i>
                            <span>Confirm & Promote Batch</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
