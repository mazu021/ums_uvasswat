@extends('layouts.app')

@section('title', 'Student ID Card Generator')
@section('header_title', 'Student Smart ID Card Studio & Generator')

@section('content')
<div class="space-y-6" x-data="{
    activeTemplate: 'uvas_official',
    cardSide: 'both', // 'front', 'back', 'both'
    selectedStudentId: '{{ $students->first()->id ?? '' }}',
    selectedStudentsMap: {},
    studentsList: {{ json_encode($students->map(function($s) {
        return [
            'id' => $s->id,
            'name' => strtoupper($s->full_name),
            'father_name' => $s->father_name ?? 'N/A',
            'reg' => $s->registration_number,
            'roll' => $s->roll_number ?? 'N/A',
            'cnic' => $s->cnic ?? '15604-0382699-1',
            'phone' => $s->phone ?? '03414432963',
            'email' => $s->email ?? 'student@uvasswat.edu.pk',
            'blood_group' => $s->blood_group ?? 'B+',
            'department_id' => $s->department_id,
            'department' => strtoupper($s->department->name ?? 'COMPUTER SCIENCE'),
            'dept_code' => strtoupper($s->department->code ?? 'CS'),
            'semester' => $s->current_semester,
            'gender' => ucfirst($s->gender ?? 'Male'),
            'address' => $s->address ?? 'Kabal, Swat, Khyber Pakhtunkhwa',
            'session' => '2025 - 2029',
            'valid_till' => '31 DEC 2029',
            'issue_date' => '01 AUG 2025'
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
            <h3 class="text-xl font-bold text-slate-800">Official UVAS Swat Student ID Card Studio</h3>
            <p class="text-xs text-slate-500">Generate, preview, and print official dual-sided PVC student identity cards.</p>
        </div>

        <div class="flex items-center space-x-3">
            <button @click="printCards()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span x-text="'Print ID Cards (' + getSelectedForPrint().length + ' Selected)'"></span>
            </button>
        </div>
    </div>

    <!-- Filter & Template Selection Bar (Hidden during Print) -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 print:hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

            <!-- Template Picker -->
            <div class="space-y-2">
                <label class="block font-extrabold text-slate-900 uppercase text-[10px] tracking-wider">Select ID Card Design Template</label>
                <div class="flex flex-wrap items-center gap-2">
                    <button @click="activeTemplate = 'uvas_official'" :class="activeTemplate === 'uvas_official' ? 'bg-[#373887] text-white border-[#373887] font-extrabold shadow-md' : 'bg-slate-50 text-slate-700 border-slate-200 font-bold'" class="px-4 py-2 text-xs rounded-xl border transition flex items-center space-x-2">
                        <i class="fa-solid fa-certificate text-amber-400"></i>
                        <span>Template 1: Official UVAS SWAT (Dual-Sided)</span>
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

            <!-- Card Side Switcher (Only active when Template 1 is selected) -->
            <div x-show="activeTemplate === 'uvas_official'" class="space-y-2">
                <label class="block font-extrabold text-slate-900 uppercase text-[10px] tracking-wider">Preview Card Side</label>
                <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 text-xs font-bold">
                    <button @click="cardSide = 'front'" :class="cardSide === 'front' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg transition">Front Only</button>
                    <button @click="cardSide = 'back'" :class="cardSide === 'back' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg transition">Back Only</button>
                    <button @click="cardSide = 'both'" :class="cardSide === 'both' ? 'bg-white text-slate-900 shadow-sm font-extrabold' : 'text-slate-500'" class="px-3 py-1.5 rounded-lg transition">Both Sides</button>
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

        <!-- Left Column: Student Registry Selector (4 Cols) -->
        <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col max-h-[720px]">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Student Registry</span>
                <div class="flex items-center space-x-2">
                    <button @click="toggleAll(true)" class="px-2.5 py-1 bg-slate-200 hover:bg-slate-300 font-bold text-[10px] rounded-md text-slate-700">Select All</button>
                    <button @click="toggleAll(false)" class="px-2.5 py-1 bg-slate-200 hover:bg-slate-300 font-bold text-[10px] rounded-md text-slate-700">Deselect</button>
                </div>
            </div>

            <div class="overflow-y-auto flex-1 divide-y divide-slate-100">
                <template x-for="st in studentsList" :key="st.id">
                    <div @click="selectedStudentId = st.id" :class="selectedStudentId == st.id ? 'bg-indigo-50/80 border-l-4 border-[#373887]' : 'hover:bg-slate-50'" class="p-3.5 flex items-center justify-between cursor-pointer transition">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" :value="st.id" x-model="selectedStudentsMap[st.id]" @click.stop class="rounded border-slate-300 text-[#373887] focus:ring-[#373887]">
                            <div>
                                <h5 class="font-extrabold text-xs text-slate-900" x-text="st.name"></h5>
                                <span class="text-[10px] font-mono text-slate-500" x-text="st.reg + ' • ' + st.dept_code"></span>
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

        <!-- Right Column: Interactive Live Card Preview (8 Cols) -->
        <div class="lg:col-span-8 bg-slate-900 p-8 rounded-3xl shadow-xl flex flex-col items-center justify-center space-y-6 border border-slate-800 min-h-[580px]">
            <div class="text-center space-y-1">
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] rounded-full uppercase tracking-wider border border-emerald-500/30">
                    Live Official ID Card Studio Preview
                </span>
                <h4 class="text-sm font-bold text-white" x-text="'Student: ' + (getSelectedStudent() ? getSelectedStudent().name : 'No Student Selected')"></h4>
            </div>

            <!-- CARD CANVAS CONTAINERS -->
            <div class="p-6 bg-slate-800/80 rounded-3xl border border-slate-700/60 shadow-2xl flex flex-wrap items-center justify-center gap-8">

                <!-- TEMPLATE 1: OFFICIAL UVAS SWAT DUAL-SIDED ID CARD -->
                <template x-if="activeTemplate === 'uvas_official' && getSelectedStudent()">
                    <div class="flex flex-wrap items-center justify-center gap-8">

                        <!-- FRONT SIDE CARD -->
                        <div x-show="cardSide === 'front' || cardSide === 'both'" class="w-[260px] h-[450px] bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-300 flex relative text-slate-900 select-none">
                            
                            <!-- Left Sidebar Band -->
                            <div class="w-[52px] bg-[#373887] text-white flex flex-col items-center justify-center py-6 relative overflow-hidden shrink-0">
                                <div class="rotate-[-90deg] whitespace-nowrap tracking-[0.45em] font-black text-[13px] uppercase text-white drop-shadow-sm">
                                    UVAS SWAT
                                </div>
                            </div>

                            <!-- Right Main Body -->
                            <div class="flex-1 p-4 flex flex-col items-center justify-between text-center space-y-2 bg-gradient-to-b from-white via-slate-50/50 to-white">
                                <!-- Top Crest Official Logo -->
                                <div class="flex flex-col items-center">
                                    <img src="{{ asset('images/uvas_official_logo.png') }}" alt="UVAS Logo" class="w-16 h-16 object-contain drop-shadow-xs">
                                </div>

                                <!-- Student Photo -->
                                <div class="w-24 h-24 rounded-full border-4 border-slate-200 shadow-md overflow-hidden bg-slate-100 flex items-center justify-center text-slate-400 text-3xl font-bold">
                                    <i class="fa-solid fa-user"></i>
                                </div>

                                <!-- Student Info -->
                                <div class="space-y-1">
                                    <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight leading-tight" x-text="getSelectedStudent().name"></h3>
                                    <p class="text-[10px] font-black text-slate-600 tracking-widest uppercase">STUDENT</p>
                                </div>

                                <!-- Department Box -->
                                <div class="space-y-0.5">
                                    <p class="text-[10px] font-black text-slate-900 tracking-widest uppercase">DEPARTMENT</p>
                                    <p class="text-[11px] font-bold text-slate-700 uppercase leading-snug px-1" x-text="getSelectedStudent().department"></p>
                                </div>

                                <!-- QR Code & Card ID -->
                                <div class="flex flex-col items-center pt-1 space-y-1">
                                    <div class="w-14 h-14 bg-slate-900 text-white rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-qrcode text-2xl"></i>
                                    </div>
                                    <span class="font-mono font-black text-[10px] text-slate-900 uppercase" x-text="'CARD ID: ' + getSelectedStudent().reg"></span>
                                </div>
                            </div>
                        </div>

                        <!-- BACK SIDE CARD -->
                        <div x-show="cardSide === 'back' || cardSide === 'both'" class="w-[260px] h-[450px] bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-300 flex flex-col justify-between relative text-slate-900 select-none">
                            
                            <!-- Top Polygon Accent Header -->
                            <div class="h-16 bg-[#373887] relative flex items-center justify-center clip-path-polygon">
                                <div class="w-11 h-11 bg-white rounded-full p-1 shadow-md flex items-center justify-center">
                                    <img src="{{ asset('images/uvas_official_logo.png') }}" alt="UVAS Logo" class="w-9 h-9 object-contain">
                                </div>
                            </div>

                            <!-- Back Content Table -->
                            <div class="px-4 py-2 flex-1 space-y-1.5 text-[10px] font-medium text-slate-800">
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="font-bold text-slate-900">Father Name:</span>
                                    <span class="font-bold text-slate-800 text-right truncate max-w-[130px]" x-text="getSelectedStudent().father_name"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="font-bold text-slate-900">Gender :</span>
                                    <span class="font-bold text-slate-800" x-text="getSelectedStudent().gender"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="font-bold text-slate-900">Cnic no:</span>
                                    <span class="font-bold font-mono text-slate-800" x-text="getSelectedStudent().cnic"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="font-bold text-slate-900">Contact No:</span>
                                    <span class="font-bold font-mono text-slate-800" x-text="getSelectedStudent().phone"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="font-bold text-slate-900">Session:</span>
                                    <span class="font-bold text-slate-800" x-text="getSelectedStudent().session"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="font-bold text-slate-900">Discipline:</span>
                                    <span class="font-bold text-slate-800 uppercase" x-text="getSelectedStudent().dept_code"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="font-bold text-slate-900">Blood Group:</span>
                                    <span class="font-black text-rose-600" x-text="getSelectedStudent().blood_group"></span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="font-bold text-slate-900">Email Address:</span>
                                    <span class="font-bold text-slate-700 text-right truncate max-w-[120px]" x-text="getSelectedStudent().email"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-bold text-slate-900">Address:</span>
                                    <span class="font-medium text-slate-700 text-right truncate max-w-[130px]" x-text="getSelectedStudent().address"></span>
                                </div>
                            </div>

                            <!-- Footer Section -->
                            <div class="px-3 pb-3 pt-1 text-center space-y-1">
                                <div class="h-1 bg-slate-900 w-full mb-1"></div>
                                <p class="font-extrabold text-[9px] text-slate-900 leading-tight">If Found, Please Post To</p>
                                <p class="text-[8px] font-bold text-slate-700 leading-tight">Office of the Director Student Affairs (DSA)</p>
                                <p class="text-[7px] text-slate-500 font-semibold">The University Of Veterinary & Animal Sciences</p>
                                <p class="text-[7px] text-emerald-700 font-extrabold">www.uvasswat.edu.pk</p>

                                <!-- Signature Line -->
                                <div class="pt-2 flex flex-col items-center">
                                    <i class="fa-solid fa-signature text-slate-700 text-sm"></i>
                                    <div class="w-32 h-0.5 bg-slate-800 my-0.5"></div>
                                    <span class="text-[8px] font-extrabold text-slate-900 uppercase">Director Student Affairs</span>
                                </div>
                            </div>

                            <!-- Bottom Polygon Accent Footer -->
                            <div class="h-6 bg-[#373887]"></div>
                        </div>

                    </div>
                </template>

                <!-- TEMPLATE 2: SLEEK DARK GLASSMORPHISM -->
                <template x-if="activeTemplate === 'dark_glass' && getSelectedStudent()">
                    <div class="w-[280px] h-[450px] bg-slate-950 rounded-2xl shadow-2xl overflow-hidden border border-slate-800 flex flex-col justify-between relative text-white">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/10 via-transparent to-indigo-600/20 pointer-events-none"></div>

                        <!-- Top Header -->
                        <div class="p-4 text-center border-b border-slate-800 space-y-1 relative z-10">
                            <img src="{{ asset('images/uvas_official_logo.png') }}" alt="UVAS Logo" class="w-12 h-12 object-contain mx-auto">
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
                        <div class="bg-indigo-900 px-4 py-2 text-white flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <img src="{{ asset('images/uvas_official_logo.png') }}" alt="UVAS Logo" class="w-10 h-10 object-contain bg-white rounded-full p-0.5">
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
        <div class="grid grid-cols-2 gap-8 p-4">
            <template x-for="st in getSelectedForPrint()" :key="st.id">
                <div class="flex justify-center mb-8 break-inside-avoid">

                    <!-- PRINT TEMPLATE 1: OFFICIAL UVAS SWAT DUAL-SIDED ID CARD -->
                    <template x-if="activeTemplate === 'uvas_official'">
                        <div class="flex items-center justify-center gap-6">

                            <!-- FRONT -->
                            <div class="w-[260px] h-[450px] bg-white rounded-2xl border-2 border-slate-800 flex relative text-slate-900 text-left">
                                <div class="w-[52px] bg-[#373887] text-white flex flex-col items-center justify-center py-6 relative overflow-hidden shrink-0">
                                    <div class="rotate-[-90deg] whitespace-nowrap tracking-[0.45em] font-black text-[13px] uppercase text-white">
                                        UVAS SWAT
                                    </div>
                                </div>
                                <div class="flex-1 p-4 flex flex-col items-center justify-between text-center space-y-2">
                                    <div class="flex flex-col items-center">
                                        <img src="{{ asset('images/uvas_official_logo.png') }}" alt="UVAS Logo" class="w-16 h-16 object-contain">
                                    </div>
                                    <div class="w-24 h-24 rounded-full border-4 border-slate-300 overflow-hidden bg-slate-100 flex items-center justify-center text-slate-400 text-3xl font-bold">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-sm text-slate-900 uppercase" x-text="st.name"></h3>
                                        <p class="text-[10px] font-black text-slate-600 tracking-widest uppercase">STUDENT</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-900 tracking-widest uppercase">DEPARTMENT</p>
                                        <p class="text-[11px] font-bold text-slate-700 uppercase leading-snug" x-text="st.department"></p>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-qrcode text-2xl text-slate-900"></i>
                                        <span class="font-mono font-black text-[10px] text-slate-900 uppercase" x-text="'CARD ID: ' + st.reg"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- BACK -->
                            <div class="w-[260px] h-[450px] bg-white rounded-2xl border-2 border-slate-800 flex flex-col justify-between relative text-slate-900 text-left">
                                <div class="h-14 bg-[#373887] flex items-center justify-center">
                                    <div class="w-9 h-9 bg-white rounded-full flex items-center justify-center">
                                        <img src="{{ asset('images/uvas_official_logo.png') }}" alt="UVAS Logo" class="w-8 h-8 object-contain">
                                    </div>
                                </div>
                                <div class="px-4 py-2 flex-1 space-y-1 text-[10px] font-medium text-slate-800">
                                    <div class="flex justify-between border-b pb-1"><strong>Father Name:</strong> <span x-text="st.father_name"></span></div>
                                    <div class="flex justify-between border-b pb-1"><strong>Gender :</strong> <span x-text="st.gender"></span></div>
                                    <div class="flex justify-between border-b pb-1"><strong>Cnic no:</strong> <span class="font-mono" x-text="st.cnic"></span></div>
                                    <div class="flex justify-between border-b pb-1"><strong>Contact No:</strong> <span class="font-mono" x-text="st.phone"></span></div>
                                    <div class="flex justify-between border-b pb-1"><strong>Session:</strong> <span x-text="st.session"></span></div>
                                    <div class="flex justify-between border-b pb-1"><strong>Discipline:</strong> <span class="uppercase" x-text="st.dept_code"></span></div>
                                    <div class="flex justify-between border-b pb-1"><strong>Blood Group:</strong> <span class="font-bold text-rose-600" x-text="st.blood_group"></span></div>
                                    <div class="flex justify-between border-b pb-1"><strong>Email Address:</strong> <span x-text="st.email"></span></div>
                                    <div class="flex justify-between"><strong>Address:</strong> <span x-text="st.address"></span></div>
                                </div>
                                <div class="px-3 pb-3 text-center space-y-0.5">
                                    <div class="h-1 bg-slate-900 w-full mb-1"></div>
                                    <p class="font-extrabold text-[9px]">If Found, Please Post To</p>
                                    <p class="text-[8px] font-bold text-slate-700">Office of the Director Student Affairs (DSA)</p>
                                    <p class="text-[7px] text-slate-500">The University Of Veterinary & Animal Sciences</p>
                                    <p class="text-[7px] text-emerald-700 font-bold">www.uvasswat.edu.pk</p>
                                    <div class="pt-1 flex flex-col items-center">
                                        <div class="w-28 h-0.5 bg-slate-800 my-0.5"></div>
                                        <span class="text-[8px] font-extrabold uppercase">Director Student Affairs</span>
                                    </div>
                                </div>
                                <div class="h-5 bg-[#373887]"></div>
                            </div>

                        </div>
                    </template>

                </div>
            </template>
        </div>
    </div>

</div>
@endsection
