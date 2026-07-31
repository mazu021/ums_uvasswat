@extends('layouts.app')

@section('title', 'Student ID Card Generator')
@section('header_title', 'Student Smart ID Card Studio & Generator')

@section('content')
<div class="space-y-6" x-data="{
    activeTemplate: 'vertical_green',
    selectedStudentId: '{{ $students->first()->id ?? '' }}',
    selectedStudentsMap: {},
    studentsList: {{ json_encode($students->map(function($s) {
        return [
            'id' => $s->id,
            'name' => $s->full_name,
            'father_name' => $s->father_name ?? 'N/A',
            'reg' => $s->registration_number,
            'roll' => $s->roll_number ?? 'N/A',
            'cnic' => $s->cnic ?? 'N/A',
            'phone' => $s->phone ?? '+92-333-9240401',
            'blood_group' => $s->blood_group ?? 'B+',
            'department_id' => $s->department_id,
            'department' => $s->department->name ?? 'General Studies',
            'dept_code' => $s->department->code ?? 'UVAS',
            'semester' => $s->current_semester,
            'gender' => $s->gender ?? 'Male',
            'valid_till' => '31 DEC 2028',
            'issue_date' => '01 AUG 2026'
        ];
    })) }},
    getSelectedStudent() {
        return this.studentsList.find(s => s.id == this.selectedStudentId) || (this.studentsList[0] || null);
    },
    toggleAll(checked) {
        this.studentsList.forEach(s => { this.selectedStudentsMap[s.id] = checked; });
    },
    getSelectedForPrint() {
        const ids = Object.keys(this.selectedStudentsMap).filter(id => this.selectedStudentsMap[id]);
        if (ids.length === 0 && this.selectedStudentId) {
            return this.studentsList.filter(s => s.id == this.selectedStudentId);
        }
        return this.studentsList.filter(s => ids.includes(String(s.id)));
    },
    printCards() {
        window.print();
    }
}">

    <!-- Top Action Bar (Hidden during Print) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Student ID Card Studio</h3>
            <p class="text-xs text-slate-500">Generate, customize, and print official PVC student identity cards dynamically.</p>
        </div>

        <div class="flex items-center space-x-3">
            <button @click="printCards()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span x-text="'Print Cards (' + getSelectedForPrint().length + ' Selected)'"></span>
            </button>
        </div>
    </div>

    <!-- Filter & Template Selection Bar (Hidden during Print) -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 print:hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

            <!-- Template Picker -->
            <div>
                <label class="block font-extrabold text-slate-900 uppercase text-[10px] tracking-wider mb-2">Select ID Card Design Template</label>
                <div class="flex items-center space-x-2">
                    <button @click="activeTemplate = 'vertical_green'" :class="activeTemplate === 'vertical_green' ? 'bg-emerald-600 text-white border-emerald-600 font-extrabold shadow-md' : 'bg-slate-50 text-slate-700 border-slate-200 font-bold'" class="px-4 py-2 text-xs rounded-xl border transition flex items-center space-x-2">
                        <i class="fa-solid fa-address-card"></i>
                        <span>Template 1: Modern Vertical</span>
                    </button>

                    <button @click="activeTemplate = 'dark_glass'" :class="activeTemplate === 'dark_glass' ? 'bg-slate-900 text-white border-slate-900 font-extrabold shadow-md' : 'bg-slate-50 text-slate-700 border-slate-200 font-bold'" class="px-4 py-2 text-xs rounded-xl border transition flex items-center space-x-2">
                        <i class="fa-solid fa-id-badge"></i>
                        <span>Template 2: Sleek Dark</span>
                    </button>

                    <button @click="activeTemplate = 'horizontal_exec'" :class="activeTemplate === 'horizontal_exec' ? 'bg-indigo-700 text-white border-indigo-700 font-extrabold shadow-md' : 'bg-slate-50 text-slate-700 border-slate-200 font-bold'" class="px-4 py-2 text-xs rounded-xl border transition flex items-center space-x-2">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Template 3: Executive Landscape</span>
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('academics.students.id-cards') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Reg No or Name..." class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 w-44">
                <select name="department_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ $d->id == $departmentId ? 'selected' : '' }}>{{ $d->code }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl">Filter</button>
            </form>

        </div>
    </div>

    <!-- Main Live Workspace & Card List (Hidden during Print) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 print:hidden">

        <!-- Left Column: Student Registry Selector (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col max-h-[680px]">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Select Students for Printing</span>
                <div class="flex items-center space-x-2">
                    <button @click="toggleAll(true)" class="px-2.5 py-1 bg-slate-200 hover:bg-slate-300 font-bold text-[10px] rounded-md text-slate-700">Select All</button>
                    <button @click="toggleAll(false)" class="px-2.5 py-1 bg-slate-200 hover:bg-slate-300 font-bold text-[10px] rounded-md text-slate-700">Deselect</button>
                </div>
            </div>

            <div class="overflow-y-auto flex-1 divide-y divide-slate-100">
                <template x-for="st in studentsList" :key="st.id">
                    <div @click="selectedStudentId = st.id" :class="selectedStudentId == st.id ? 'bg-emerald-50/80 border-l-4 border-emerald-600' : 'hover:bg-slate-50'" class="p-3.5 flex items-center justify-between cursor-pointer transition">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" :value="st.id" x-model="selectedStudentsMap[st.id]" @click.stop class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <h5 class="font-extrabold text-xs text-slate-900" x-text="st.name"></h5>
                                <span class="text-[10px] font-mono text-slate-500" x-text="st.reg + ' • ' + st.department"></span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 bg-slate-100 font-bold text-[10px] text-slate-700 rounded-full" x-text="'Sem ' + st.semester"></span>
                    </div>
                </template>

                <template x-if="studentsList.length === 0">
                    <div class="p-8 text-center text-slate-400 font-medium text-xs">No students match filter criteria.</div>
                </template>
            </div>
        </div>

        <!-- Right Column: Interactive Live Card Preview (7 Cols) -->
        <div class="lg:col-span-7 bg-slate-900 p-8 rounded-3xl shadow-xl flex flex-col items-center justify-center space-y-4 border border-slate-800">
            <div class="text-center space-y-1">
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] rounded-full uppercase tracking-wider border border-emerald-500/30">
                    Live ID Card Preview
                </span>
                <h4 class="text-sm font-bold text-white" x-text="'Previewing: ' + (getSelectedStudent() ? getSelectedStudent().name : 'No Student Selected')"></h4>
            </div>

            <!-- CARD CANVAS CONTAINERS -->
            <div class="p-4 bg-slate-800/60 rounded-3xl border border-slate-700/50 shadow-2xl flex justify-center">

                <!-- TEMPLATE 1: MODERN VERTICAL -->
                <template x-if="activeTemplate === 'vertical_green' && getSelectedStudent()">
                    <div class="w-[300px] h-[480px] bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-300 flex flex-col justify-between relative text-slate-800">
                        <!-- Top Header -->
                        <div class="bg-gradient-to-r from-emerald-800 to-emerald-600 p-3 text-white text-center space-y-0.5">
                            <div class="flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-graduation-cap text-amber-400 text-sm"></i>
                                <span class="font-black text-xs tracking-wider uppercase">UVAS SWAT</span>
                            </div>
                            <p class="text-[9px] font-bold text-emerald-100 tracking-tight uppercase">Univ. of Veterinary & Animal Sciences</p>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 flex-1 flex flex-col items-center justify-between text-center space-y-2">
                            <!-- Photo Avatar -->
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full border-4 border-emerald-600 overflow-hidden shadow-md bg-slate-100 flex items-center justify-center text-slate-400 text-3xl font-bold">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <span class="absolute bottom-0 right-0 px-2 py-0.5 bg-amber-400 text-slate-900 font-extrabold text-[9px] rounded-full uppercase shadow-xs">STUDENT</span>
                            </div>

                            <!-- Name & Reg -->
                            <div class="space-y-0.5">
                                <h3 class="font-black text-base text-slate-900 leading-tight uppercase" x-text="getSelectedStudent().name"></h3>
                                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 font-black text-[11px] rounded-md font-mono inline-block" x-text="getSelectedStudent().reg"></span>
                            </div>

                            <!-- Details Table -->
                            <div class="w-full text-[10px] space-y-1 bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 text-left font-bold text-slate-700">
                                <div class="flex justify-between border-b border-slate-200 pb-1">
                                    <span class="text-slate-400">Department:</span>
                                    <span class="text-slate-900 font-extrabold text-right truncate max-w-[150px]" x-text="getSelectedStudent().department"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-200 pb-1">
                                    <span class="text-slate-400">Roll Number:</span>
                                    <span class="text-slate-900 font-mono" x-text="getSelectedStudent().roll"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Blood Group:</span>
                                    <span class="text-rose-600 font-black" x-text="getSelectedStudent().blood_group"></span>
                                </div>
                            </div>

                            <!-- Barcode Representation -->
                            <div class="w-full flex flex-col items-center justify-center pt-1">
                                <div class="h-6 bg-slate-900 w-44 rounded flex items-center justify-center text-[8px] font-mono text-white tracking-widest uppercase">
                                    |||||| ||| ||||||| |||||
                                </div>
                                <span class="text-[8px] font-mono text-slate-400 mt-0.5" x-text="getSelectedStudent().reg"></span>
                            </div>
                        </div>

                        <!-- Footer Bar -->
                        <div class="bg-slate-900 px-3 py-1.5 text-white flex items-center justify-between text-[9px] font-bold">
                            <span class="text-slate-400">Valid: <strong class="text-emerald-400" x-text="getSelectedStudent().valid_till"></strong></span>
                            <span class="text-slate-300">Registrar Sign <i class="fa-solid fa-signature text-amber-400 ms-1"></i></span>
                        </div>
                    </div>
                </template>

                <!-- TEMPLATE 2: SLEEK DARK GLASSMORPHISM -->
                <template x-if="activeTemplate === 'dark_glass' && getSelectedStudent()">
                    <div class="w-[300px] h-[480px] bg-slate-950 rounded-2xl shadow-2xl overflow-hidden border border-slate-800 flex flex-col justify-between relative text-white">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/10 via-transparent to-indigo-600/20 pointer-events-none"></div>

                        <!-- Top Header -->
                        <div class="p-4 text-center border-b border-slate-800 space-y-1 relative z-10">
                            <h4 class="font-black text-sm text-emerald-400 tracking-wider uppercase">UVAS SWAT</h4>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">University of Veterinary & Animal Sciences</p>
                        </div>

                        <!-- Body -->
                        <div class="p-4 flex-1 flex flex-col items-center justify-between text-center space-y-2 relative z-10">
                            <div class="w-24 h-24 rounded-2xl border-2 border-emerald-400/80 shadow-xl overflow-hidden bg-slate-900 flex items-center justify-center text-emerald-400 text-3xl font-bold">
                                <i class="fa-solid fa-user-astronaut"></i>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-white uppercase tracking-tight" x-text="getSelectedStudent().name"></h3>
                                <p class="text-xs font-mono font-bold text-emerald-400 mt-0.5" x-text="getSelectedStudent().reg"></p>
                            </div>

                            <div class="w-full bg-slate-900/80 p-3 rounded-xl border border-slate-800 text-[10px] space-y-1.5 text-left font-semibold">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Program:</span>
                                    <span class="text-slate-200 font-bold truncate max-w-[150px]" x-text="getSelectedStudent().department"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Current Sem:</span>
                                    <span class="text-emerald-400 font-bold" x-text="'Semester ' + getSelectedStudent().semester"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Contact:</span>
                                    <span class="text-slate-300 font-mono" x-text="getSelectedStudent().phone"></span>
                                </div>
                            </div>

                            <!-- QR Code Box -->
                            <div class="p-2 bg-white rounded-xl flex items-center space-x-2 text-slate-900 w-full">
                                <div class="w-10 h-10 bg-slate-900 text-white flex items-center justify-center font-bold text-xs rounded-lg">
                                    <i class="fa-solid fa-qrcode text-lg"></i>
                                </div>
                                <div class="text-left text-[9px] font-bold">
                                    <p class="text-slate-900 uppercase">Verifiable Identity</p>
                                    <p class="text-slate-500 font-mono text-[8px]" x-text="'ID: ' + getSelectedStudent().reg"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-4 py-2 bg-slate-900 border-t border-slate-800 flex items-center justify-between text-[9px] font-bold text-slate-400 relative z-10">
                            <span>EXP: <strong class="text-white" x-text="getSelectedStudent().valid_till"></strong></span>
                            <span class="text-emerald-400 uppercase">OFFICIAL BADGE</span>
                        </div>
                    </div>
                </template>

                <!-- TEMPLATE 3: EXECUTIVE LANDSCAPE -->
                <template x-if="activeTemplate === 'horizontal_exec' && getSelectedStudent()">
                    <div class="w-[450px] h-[280px] bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-300 flex flex-col justify-between text-slate-800">
                        <!-- Top Banner -->
                        <div class="bg-indigo-900 px-4 py-2.5 text-white flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-university text-amber-400 text-base"></i>
                                <div>
                                    <h4 class="font-black text-xs tracking-wider uppercase">UVAS SWAT</h4>
                                    <p class="text-[8px] text-indigo-200 font-semibold uppercase">Univ. of Veterinary & Animal Sciences Swat</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 bg-amber-400 text-slate-900 font-black text-[9px] rounded uppercase">STUDENT CARD</span>
                        </div>

                        <!-- Body Grid -->
                        <div class="p-4 flex-1 grid grid-cols-12 gap-3 items-center">
                            <!-- Avatar Column (4 cols) -->
                            <div class="col-span-4 flex flex-col items-center justify-center space-y-1.5 border-r border-slate-200 pr-2">
                                <div class="w-20 h-20 rounded-xl border-2 border-indigo-700 overflow-hidden bg-slate-100 flex items-center justify-center text-slate-400 text-2xl font-bold">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <span class="font-mono text-[9px] font-bold text-slate-500" x-text="getSelectedStudent().reg"></span>
                                <div class="w-full bg-slate-900 text-white font-mono text-[7px] py-1 text-center rounded tracking-widest">
                                    |||||||||||||||||||||
                                </div>
                            </div>

                            <!-- Details Column (8 cols) -->
                            <div class="col-span-8 space-y-2 text-left text-[10px]">
                                <div>
                                    <h3 class="font-black text-sm text-slate-900 uppercase leading-tight" x-text="getSelectedStudent().name"></h3>
                                    <p class="text-[10px] text-indigo-700 font-extrabold" x-text="getSelectedStudent().department"></p>
                                </div>

                                <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-slate-700 font-semibold bg-slate-50 p-2 rounded-lg border border-slate-200">
                                    <div>Roll No: <strong class="text-slate-900 font-mono" x-text="getSelectedStudent().roll"></strong></div>
                                    <div>Semester: <strong class="text-slate-900" x-text="getSelectedStudent().semester"></strong></div>
                                    <div>Father Name: <strong class="text-slate-900" x-text="getSelectedStudent().father_name"></strong></div>
                                    <div>Blood Group: <strong class="text-rose-600 font-bold" x-text="getSelectedStudent().blood_group"></strong></div>
                                </div>

                                <div class="flex items-center justify-between text-[8px] text-slate-400 pt-1">
                                    <span>Issue: <strong class="text-slate-700" x-text="getSelectedStudent().issue_date"></strong></span>
                                    <span>Expiry: <strong class="text-slate-700" x-text="getSelectedStudent().valid_till"></strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-100 px-4 py-1.5 border-t border-slate-200 flex items-center justify-between text-[9px] font-bold text-slate-500">
                            <span>Campus Helpline: +92-946-9240401</span>
                            <span class="text-indigo-900 font-extrabold">Authorized Signature <i class="fa-solid fa-signature text-emerald-600 ms-1"></i></span>
                        </div>
                    </div>
                </template>

            </div>
        </div>

    </div>

    <!-- PRINT SHEET LAYOUT (ONLY VISIBLE DURING PRINT) -->
    <div class="hidden print:block space-y-6">
        <div class="grid grid-cols-2 gap-6 p-4">
            <template x-for="st in getSelectedForPrint()" :key="st.id">
                <div class="flex justify-center mb-6 break-inside-avoid">

                    <!-- PRINT TEMPLATE 1: MODERN VERTICAL -->
                    <template x-if="activeTemplate === 'vertical_green'">
                        <div class="w-[300px] h-[480px] bg-white rounded-2xl border-2 border-slate-800 flex flex-col justify-between text-slate-800 text-left">
                            <div class="bg-emerald-800 p-3 text-white text-center">
                                <h4 class="font-black text-xs uppercase">UVAS SWAT</h4>
                                <p class="text-[9px] font-bold">Univ. of Veterinary & Animal Sciences</p>
                            </div>
                            <div class="p-4 flex-1 flex flex-col items-center justify-between text-center space-y-2">
                                <div class="w-24 h-24 rounded-full border-4 border-emerald-600 overflow-hidden bg-slate-100 flex items-center justify-center text-slate-400 text-3xl font-bold">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-base text-slate-900 uppercase" x-text="st.name"></h3>
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-black text-xs font-mono rounded" x-text="st.reg"></span>
                                </div>
                                <div class="w-full text-[10px] space-y-1 bg-slate-50 p-2 rounded border font-bold text-slate-700 text-left">
                                    <div>Dept: <strong x-text="st.department"></strong></div>
                                    <div>Roll: <strong class="font-mono" x-text="st.roll"></strong></div>
                                    <div>Blood Group: <strong class="text-rose-600" x-text="st.blood_group"></strong></div>
                                </div>
                                <div class="w-full bg-slate-900 text-white font-mono text-[8px] py-1 text-center tracking-widest uppercase">
                                    |||||| ||| ||||||| |||||
                                </div>
                            </div>
                            <div class="bg-slate-900 px-3 py-1.5 text-white flex justify-between text-[9px] font-bold">
                                <span>Valid: <strong class="text-emerald-400" x-text="st.valid_till"></strong></span>
                                <span>Registrar Sign</span>
                            </div>
                        </div>
                    </template>

                    <!-- PRINT TEMPLATE 2: SLEEK DARK -->
                    <template x-if="activeTemplate === 'dark_glass'">
                        <div class="w-[300px] h-[480px] bg-slate-950 rounded-2xl border-2 border-slate-800 flex flex-col justify-between text-white text-left">
                            <div class="p-4 text-center border-b border-slate-800">
                                <h4 class="font-black text-sm text-emerald-400 uppercase">UVAS SWAT</h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">University of Veterinary Sciences</p>
                            </div>
                            <div class="p-4 flex-1 flex flex-col items-center justify-between text-center space-y-2">
                                <div class="w-24 h-24 rounded-2xl border-2 border-emerald-400 overflow-hidden bg-slate-900 flex items-center justify-center text-emerald-400 text-3xl font-bold">
                                    <i class="fa-solid fa-user-astronaut"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-base text-white uppercase" x-text="st.name"></h3>
                                    <p class="text-xs font-mono font-bold text-emerald-400" x-text="st.reg"></p>
                                </div>
                                <div class="w-full bg-slate-900 p-2 rounded border border-slate-800 text-[10px] space-y-1 text-left font-semibold">
                                    <div>Program: <strong class="text-slate-200" x-text="st.department"></strong></div>
                                    <div>Semester: <strong class="text-emerald-400" x-text="'Sem ' + st.semester"></strong></div>
                                </div>
                            </div>
                            <div class="px-4 py-2 bg-slate-900 border-t border-slate-800 flex justify-between text-[9px] font-bold text-slate-400">
                                <span>EXP: <strong class="text-white" x-text="st.valid_till"></strong></span>
                                <span class="text-emerald-400 uppercase">OFFICIAL BADGE</span>
                            </div>
                        </div>
                    </template>

                    <!-- PRINT TEMPLATE 3: EXECUTIVE LANDSCAPE -->
                    <template x-if="activeTemplate === 'horizontal_exec'">
                        <div class="w-[450px] h-[280px] bg-white rounded-2xl border-2 border-slate-800 flex flex-col justify-between text-slate-800 text-left">
                            <div class="bg-indigo-900 px-4 py-2 text-white flex justify-between">
                                <h4 class="font-black text-xs uppercase">UVAS SWAT</h4>
                                <span class="px-2 py-0.5 bg-amber-400 text-slate-900 font-black text-[9px] rounded uppercase">STUDENT ID</span>
                            </div>
                            <div class="p-4 flex-1 grid grid-cols-12 gap-3 items-center">
                                <div class="col-span-4 flex flex-col items-center space-y-1.5 border-r border-slate-200 pr-2">
                                    <div class="w-20 h-20 rounded-xl border-2 border-indigo-700 overflow-hidden bg-slate-100 flex items-center justify-center text-slate-400 text-2xl font-bold">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <span class="font-mono text-[9px] font-bold" x-text="st.reg"></span>
                                </div>
                                <div class="col-span-8 space-y-2 text-left text-[10px]">
                                    <h3 class="font-black text-sm text-slate-900 uppercase" x-text="st.name"></h3>
                                    <p class="text-[10px] text-indigo-700 font-extrabold" x-text="st.department"></p>
                                    <div class="grid grid-cols-2 gap-1 text-slate-700 font-semibold bg-slate-50 p-2 rounded border">
                                        <div>Roll: <strong class="font-mono" x-text="st.roll"></strong></div>
                                        <div>Sem: <strong x-text="st.semester"></strong></div>
                                        <div>Blood: <strong class="text-rose-600" x-text="st.blood_group"></strong></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-100 px-4 py-1.5 border-t border-slate-200 flex justify-between text-[9px] font-bold text-slate-500">
                                <span>Helpline: +92-946-9240401</span>
                                <span class="text-indigo-900 font-extrabold">Authorized Signature</span>
                            </div>
                        </div>
                    </template>

                </div>
            </template>
        </div>
    </div>

</div>
@endsection
