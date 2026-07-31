@extends('layouts.app')

@section('title', 'Student Batch Promotion')
@section('header_title', 'Student Promotion & Semester Progression')

@section('content')
<div class="space-y-6" x-data="{
    promoteDeptId: '{{ $departments->first()->id ?? '' }}',
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
    },
    getSelectedCount() {
        return Object.values(this.selectedStudentsMap).filter(Boolean).length;
    }
}" x-init="fetchPromoteStudents()">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Student Batch Promotion Module</h3>
            <p class="text-xs text-slate-500">Bulk promote active student cohorts from their current semester to the next academic semester.</p>
        </div>
        <a href="{{ route('academics.students.index') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2 transition self-start sm:self-auto">
            <i class="fa-solid fa-users me-1"></i>
            <span>View Student Registry</span>
        </a>
    </div>

    <!-- Filter & Configuration Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
        <h4 class="font-extrabold text-sm text-slate-900 flex items-center space-x-2">
            <i class="fa-solid fa-sliders text-emerald-600"></i>
            <span>Promotion Configuration & Cohort Selection</span>
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Select Department / Program *</label>
                <select x-model="promoteDeptId" @change="fetchPromoteStudents()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Current Semester (Source) *</label>
                <select x-model="promoteCurrentSem" @change="fetchPromoteStudents()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Target Semester (Destination) *</label>
                <select x-model="promoteTargetSem" class="w-full px-3 py-2 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-xl font-extrabold focus:ring-2 focus:ring-emerald-500">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">Promote to Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <!-- Student Selection List & Submit Form -->
    <form action="{{ route('academics.students.promote-batch') }}" method="POST">
        @csrf
        <input type="hidden" name="target_semester" :value="promoteTargetSem">

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden space-y-4">
            <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="font-extrabold text-sm text-slate-900">Eligible Students Cohort</span>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-xs" x-text="promoteStudentsList.length + ' Students Found'"></span>
                </div>
                <div class="flex items-center space-x-3 text-xs" x-show="promoteStudentsList.length > 0">
                    <label class="flex items-center space-x-2 font-bold text-slate-700 cursor-pointer">
                        <input type="checkbox" @change="toggleAll($event.target.checked)" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span>Select All</span>
                    </label>
                </div>
            </div>

            <!-- Loading State -->
            <div x-show="loadingStudents" class="p-12 text-center text-slate-400 space-y-2">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl text-emerald-600"></i>
                <p class="font-bold text-xs text-slate-600">Loading student records for selected cohort...</p>
            </div>

            <!-- Students List Table -->
            <div x-show="!loadingStudents">
                <template x-if="promoteStudentsList.length > 0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-600">
                            <thead class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-center w-12">
                                        <i class="fa-solid fa-check-double text-slate-400"></i>
                                    </th>
                                    <th class="px-6 py-3">Registration No.</th>
                                    <th class="px-6 py-3">Roll No.</th>
                                    <th class="px-6 py-3">Student Name</th>
                                    <th class="px-6 py-3">Current Status</th>
                                    <th class="px-6 py-3 text-right">Promotion Path</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="st in promoteStudentsList" :key="st.id">
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" name="student_ids[]" :value="st.id" x-model="selectedStudentsMap[st.id]" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        </td>
                                        <td class="px-6 py-3 font-bold font-mono text-slate-800" x-text="st.registration_number"></td>
                                        <td class="px-6 py-3 font-bold font-mono text-slate-600" x-text="st.roll_number || 'N/A'"></td>
                                        <td class="px-6 py-3 font-extrabold text-slate-900" x-text="st.first_name + ' ' + st.last_name"></td>
                                        <td class="px-6 py-3">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800" x-text="'Sem ' + st.current_semester"></span>
                                        </td>
                                        <td class="px-6 py-3 text-right font-bold text-emerald-600">
                                            <span x-text="'Sem ' + st.current_semester"></span>
                                            <i class="fa-solid fa-arrow-right mx-1.5 text-slate-400"></i>
                                            <strong class="text-emerald-700" x-text="'Sem ' + promoteTargetSem"></strong>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template x-if="promoteStudentsList.length === 0">
                    <div class="p-12 text-center text-slate-400 space-y-2">
                        <i class="fa-solid fa-user-graduate text-3xl text-slate-300 block mb-2"></i>
                        <p class="font-bold text-slate-600 text-sm">No Active Students Found</p>
                        <p class="text-xs">There are no active students matching the selected department and current semester.</p>
                    </div>
                </template>
            </div>

            <!-- Footer Action Bar -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4" x-show="promoteStudentsList.length > 0 && !loadingStudents">
                <div class="text-xs font-bold text-slate-700 flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>Selected <strong class="text-slate-900" x-text="getSelectedCount()"></strong> of <strong class="text-slate-900" x-text="promoteStudentsList.length"></strong> students for promotion.</span>
                </div>

                <button type="submit" :disabled="getSelectedCount() === 0" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-extrabold text-xs rounded-xl shadow-lg transition flex items-center space-x-2">
                    <i class="fa-solid fa-rocket"></i>
                    <span x-text="'Promote ' + getSelectedCount() + ' Students to Semester ' + promoteTargetSem"></span>
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
