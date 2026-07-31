@extends('layouts.app')

@section('title', 'Student Transfer & Migration')
@section('header_title', 'Student Inter-Departmental Transfer & Migration')

@section('content')
<div class="space-y-6" x-data="{
    studentsList: {{ json_encode($students->map(function($s) {
        return [
            'id' => $s->id,
            'name' => $s->full_name,
            'reg' => $s->registration_number,
            'roll' => $s->roll_number ?? 'N/A',
            'department_id' => $s->department_id,
            'department_name' => $s->department->name ?? 'Unassigned',
            'semester' => $s->current_semester,
        ];
    })) }},
    selectedStudentId: '',
    selectedStudent: null,
    targetDepartmentId: '',
    targetSemester: '1',
    onStudentSelect() {
        this.selectedStudent = this.studentsList.find(s => s.id == this.selectedStudentId) || null;
        if (this.selectedStudent) {
            this.targetSemester = this.selectedStudent.semester;
        }
    }
}">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Student Transfer & Department Migration</h3>
            <p class="text-xs text-slate-500">Transfer enrolled students between departments, academic programs, and active semester cohorts.</p>
        </div>
        <a href="{{ route('academics.students.index') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition self-start sm:self-auto">
            <i class="fa-solid fa-users me-1"></i>
            <span>View Student Registry</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Card (2 Cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h4 class="font-extrabold text-sm text-slate-900 flex items-center space-x-2">
                    <i class="fa-solid fa-[#2e2e7f] fa-right-left text-emerald-600"></i>
                    <span>Execute Department Migration</span>
                </h4>
                <span class="text-xs font-bold text-slate-400">Inter-Departmental Transfer</span>
            </div>

            <form action="{{ route('academics.students.process-transfer') }}" method="POST" class="space-y-5 text-xs">
                @csrf

                <!-- Step 1: Select Student -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase text-[10px] tracking-wider">1. Select Student to Transfer *</label>
                    <select name="student_id" x-model="selectedStudentId" @change="onStudentSelect()" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 text-xs focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Choose Student by Name or Registration Number --</option>
                        <template x-for="st in studentsList" :key="st.id">
                            <option :value="st.id" x-text="st.name + ' (' + st.reg + ') - Current: ' + st.department_name + ' Sem ' + st.semester"></option>
                        </template>
                    </select>
                </div>

                <!-- Selected Student Live Preview Banner -->
                <template x-if="selectedStudent">
                    <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl space-y-2">
                        <div class="flex items-center justify-between">
                            <h5 class="font-extrabold text-slate-900 text-xs flex items-center space-x-2">
                                <i class="fa-solid fa-user-graduate text-emerald-600"></i>
                                <span x-text="selectedStudent.name"></span>
                            </h5>
                            <span class="px-2.5 py-0.5 bg-emerald-200 text-emerald-900 font-extrabold text-[10px] rounded-full" x-text="'Reg: ' + selectedStudent.reg"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-600">
                            <p>Current Department: <strong class="text-slate-800" x-text="selectedStudent.department_name"></strong></p>
                            <p>Current Semester: <strong class="text-slate-800" x-text="'Semester ' + selectedStudent.semester"></strong></p>
                        </div>
                    </div>
                </template>

                <!-- Step 2: Destination Configuration -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase text-[10px] tracking-wider">2. Destination Department *</label>
                        <select name="target_department_id" x-model="targetDepartmentId" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 text-xs focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Select Target Department --</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase text-[10px] tracking-wider">3. Destination Semester *</label>
                        <select name="target_semester" x-model="targetSemester" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 text-xs focus:ring-2 focus:ring-emerald-500">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Step 3: Migration Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase text-[10px] tracking-wider">NOC / Reference No. (Optional)</label>
                        <input type="text" name="noc_number" placeholder="e.g. NOC-UVAS-2026-088" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase text-[10px] tracking-wider">Transfer Reason / Remarks</label>
                        <input type="text" name="reason" placeholder="e.g. Credit Transfer or Inter-Departmental Migration Request" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" :disabled="!selectedStudentId || !targetDepartmentId" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-extrabold text-xs rounded-xl shadow-lg transition flex items-center space-x-2">
                        <i class="fa-solid fa-right-left"></i>
                        <span>Transfer Student Now</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Side Guidelines & Summary -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-6 rounded-3xl text-white space-y-3 shadow-md border border-slate-700">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-500/30">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h4 class="font-extrabold text-sm text-white">Migration Policy & Rules</h4>
                <ul class="text-xs text-slate-300 space-y-2 font-normal leading-relaxed list-disc list-inside">
                    <li>Student transfers automatically reassign student records to the new department roster.</li>
                    <li>Exam grades and fee history are preserved seamlessly across migrations.</li>
                    <li>Every transfer logs an audit entry with NOC reference numbers.</li>
                </ul>
            </div>
        </div>

    </div>

    <!-- Recent Transfer Audit Log History -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden space-y-4">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h4 class="font-extrabold text-sm text-slate-900 flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                <span>Recent Department Transfer History</span>
            </h4>
            <span class="text-xs font-bold text-slate-400">{{ $transferLogs->count() }} Transfers Recorded</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">S.No</th>
                        <th class="px-6 py-3.5">Student Details</th>
                        <th class="px-6 py-3.5">From Department</th>
                        <th class="px-6 py-3.5">To Department</th>
                        <th class="px-6 py-3.5">NOC / Reason</th>
                        <th class="px-6 py-3.5 text-right">Transfer Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transferLogs as $log)
                        @php
                            $details = is_array($log->details) ? $log->details : json_decode($log->details, true);
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-4 text-center font-bold text-slate-400">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $details['student_name'] ?? 'Student' }}
                                <span class="block font-mono text-[10px] text-slate-400 font-normal">{{ $details['reg_no'] ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-600">{{ $details['from_department'] ?? 'Previous' }}</td>
                            <td class="px-6 py-4 font-extrabold text-emerald-700">{{ $details['to_department'] ?? 'Target' }} (Sem {{ $details['new_semester'] ?? 1 }})</td>
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                <span class="font-bold text-slate-700">{{ $details['noc_number'] ?? 'N/A' }}</span>
                                <span class="block text-[10px] text-slate-400">{{ $details['reason'] ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-400">{{ $log->created_at ? $log->created_at->format('M d, Y • h:i A') : 'Recorded' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">No student transfers recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
