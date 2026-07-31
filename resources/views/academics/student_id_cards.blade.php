@extends('layouts.app')

@section('title', 'Student ID Card Generator')
@section('header_title', 'Student Smart ID Card Studio & Generator')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<div class="space-y-6" x-data="{
    activeTemplate: 'uvas_official',
    cardSide: 'both', // 'front', 'back', 'both'
    downloading: false,
    downloadProgress: 0,
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
    },
    async downloadZipCards() {
        if (typeof JSZip === 'undefined') {
            alert('JSZip library is loading, please try again in a moment.');
            return;
        }
        this.downloading = true;
        this.downloadProgress = 0;
        const selected = this.getSelectedForPrint();
        const zip = new JSZip();

        for (let i = 0; i < selected.length; i++) {
            const st = selected[i];
            this.downloadProgress = Math.round(((i + 1) / selected.length) * 100);

            const semFolder = zip.folder('Semester_' + st.semester);
            const frontFolder = semFolder.folder('Front_Cards');
            const backFolder = semFolder.folder('Back_Cards');

            // Export Front PNG
            const frontEl = document.getElementById('card-front-export-' + st.id);
            if (frontEl) {
                try {
                    const canvasFront = await html2canvas(frontEl, { scale: 3, useCORS: true, allowTaint: true, logging: false });
                    const dataUrlFront = canvasFront.toDataURL('image/png').replace(/^data:image\/(png|jpg);base64,/, '');
                    frontFolder.file(`UVAS_ID_FRONT_${st.name.replace(/\s+/g, '_')}_${st.reg}.png`, dataUrlFront, { base64: true });
                } catch(e) {}
            }

            // Export Back PNG
            const backEl = document.getElementById('card-back-export-' + st.id);
            if (backEl) {
                try {
                    const canvasBack = await html2canvas(backEl, { scale: 3, useCORS: true, allowTaint: true, logging: false });
                    const dataUrlBack = canvasBack.toDataURL('image/png').replace(/^data:image\/(png|jpg);base64,/, '');
                    backFolder.file(`UVAS_ID_BACK_${st.name.replace(/\s+/g, '_')}_${st.reg}.png`, dataUrlBack, { base64: true });
                } catch(e) {}
            }
        }

        const zipBlob = await zip.generateAsync({ type: 'blob' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(zipBlob);
        link.download = `UVAS_STUDENT_ID_CARDS_BATCH_${new Date().toISOString().slice(0,10)}.zip`;
        link.click();

        this.downloading = false;
    }
}">

    <!-- Header Banner & Action Bar -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-2xl border border-slate-800 print:hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-gradient-to-br from-[#373887]/40 to-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-1.5 max-w-2xl">
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 font-extrabold text-[10px] rounded-full uppercase tracking-wider border border-emerald-500/30">
                        Official Studio
                    </span>
                    <span class="text-xs text-slate-400 font-medium">• Dual-Sided Smart Identity Management</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Student Smart ID Card Studio</h2>
                <p class="text-xs sm:text-sm text-slate-400 font-medium">Design, preview, print, and bulk package department & semester PVC student ID cards in high-resolution PNG & ZIP format.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <button @click="downloadZipCards()" :disabled="downloading || getSelectedForPrint().length === 0" class="px-5 py-3 bg-[#373887] hover:bg-[#2c2d6e] disabled:opacity-50 text-white font-extrabold text-xs rounded-2xl shadow-xl transition-all duration-200 flex items-center space-x-2.5 border border-indigo-500/30">
                    <i x-show="!downloading" class="fa-solid fa-file-zipper text-amber-400 text-sm"></i>
                    <i x-show="downloading" class="fa-solid fa-spinner fa-spin text-sm"></i>
                    <span x-text="downloading ? 'Packaging ZIP (' + downloadProgress + '%)...' : 'Download All Cards ZIP (Front & Back)'"></span>
                </button>

                <button @click="printCards()" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-2xl shadow-xl transition-all duration-200 flex items-center space-x-2.5 border border-emerald-400/30">
                    <i class="fa-solid fa-print text-sm"></i>
                    <span x-text="'Print PVC Sheets (' + getSelectedForPrint().length + ' Selected)'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Structured Studio Control Toolbar -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md divide-y divide-slate-100 print:hidden overflow-hidden">
        
        <!-- Row 1: Design Template & Side Switcher -->
        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
            <!-- Templates -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Select ID Card Template</span>
                <div class="flex flex-wrap items-center gap-2">
                    <button @click="activeTemplate = 'uvas_official'" :class="activeTemplate === 'uvas_official' ? 'bg-[#373887] text-white border-[#373887] shadow-md font-extrabold scale-[1.02]' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 font-bold'" class="px-4 py-2 text-xs rounded-xl border transition-all flex items-center space-x-2">
                        <i class="fa-solid fa-certificate text-amber-400"></i>
                        <span>Template 1: Official UVAS SWAT (Dual-Sided)</span>
                    </button>

                    <button @click="activeTemplate = 'horizontal_exec'" :class="activeTemplate === 'horizontal_exec' ? 'bg-indigo-700 text-white border-indigo-700 shadow-md font-extrabold scale-[1.02]' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 font-bold'" class="px-4 py-2 text-xs rounded-xl border transition-all flex items-center space-x-2">
                        <i class="fa-solid fa-credit-card text-sky-300"></i>
                        <span>Template 2: Executive Landscape</span>
                    </button>
                </div>
            </div>

            <!-- Card Side Selector -->
            <div x-show="activeTemplate === 'uvas_official'" class="space-y-1.5">
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Preview Card Side</span>
                <div class="inline-flex p-1 bg-slate-200/60 rounded-xl text-xs font-bold border border-slate-200">
                    <button @click="cardSide = 'front'" :class="cardSide === 'front' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'" class="px-3.5 py-1.5 rounded-lg transition">Front Only</button>
                    <button @click="cardSide = 'back'" :class="cardSide === 'back' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'" class="px-3.5 py-1.5 rounded-lg transition">Back Only</button>
                    <button @click="cardSide = 'both'" :class="cardSide === 'both' ? 'bg-[#373887] text-white shadow-sm font-extrabold' : 'text-slate-600'" class="px-3.5 py-1.5 rounded-lg transition">Both Sides</button>
                </div>
            </div>
        </div>

        <!-- Row 2: Search, Department & Semester Filter Bar -->
        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white">
            <form method="GET" action="{{ route('academics.students.id-cards') }}" class="flex flex-wrap items-center gap-3 flex-1">
                
                <!-- Search Input -->
                <div class="relative min-w-[220px] flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by Reg No, Name, or Roll..." class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#373887] transition">
                </div>

                <!-- Department Selector -->
                <div class="relative min-w-[170px]">
                    <select name="department_id" onchange="this.form.submit()" class="w-full pl-3 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-[#373887] transition">
                        <option value="">🏛️ All Departments</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ $d->id == $departmentId ? 'selected' : '' }}>{{ $d->code }} - {{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Semester Selector -->
                <div class="relative min-w-[140px]">
                    <select name="semester" onchange="this.form.submit()" class="w-full pl-3 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-[#373887] transition">
                        <option value="">🎓 All Semesters</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ $semester == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Action Button -->
                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow transition">
                    Filter Registry
                </button>
            </form>

            <div class="flex items-center space-x-2 text-xs font-bold text-slate-500 shrink-0">
                <span class="px-3 py-1 bg-slate-100 text-slate-800 font-extrabold rounded-full border border-slate-200" x-text="studentsList.length + ' Students Found'"></span>
            </div>
        </div>

    </div>

    <!-- Main Live Workspace & Card List (Hidden during Print) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 print:hidden">

        <!-- Left Column: Student Registry Selector (4 Cols) -->
        <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-md overflow-hidden flex flex-col max-h-[720px]">
            <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-users text-amber-400"></i>
                    <span class="font-extrabold text-xs uppercase tracking-wider">Student Registry</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="toggleAll(true)" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 font-extrabold text-[10px] rounded-lg text-emerald-400 transition">Select All</button>
                    <button @click="toggleAll(false)" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 font-extrabold text-[10px] rounded-lg text-slate-400 transition">Deselect</button>
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

                                <!-- Dynamic QR Code & Card ID -->
                                <div class="flex flex-col items-center pt-1 space-y-1">
                                    <div class="w-14 h-14 bg-white p-1 rounded-lg border border-slate-300 shadow-xs flex items-center justify-center">
                                        <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://uvasswat.edu.pk/verify?reg=' + getSelectedStudent().reg" alt="Official UVAS QR Code" class="w-full h-full object-contain">
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

                <!-- TEMPLATE 2: EXECUTIVE LANDSCAPE -->
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
                                <div class="w-8 h-8 p-0.5 border rounded bg-white">
                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://uvasswat.edu.pk/verify?reg=' + getSelectedStudent().reg" alt="QR Code" class="w-full h-full object-contain">
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
                            <span>Website: <strong class="text-emerald-700">www.uvasswat.edu.pk</strong></span>
                            <span class="text-indigo-900 font-extrabold">Authorized Signature <i class="fa-solid fa-signature text-emerald-600 ms-1"></i></span>
                        </div>
                    </div>
                </template>

            </div>
        </div>

    </div>

    <!-- HIDDEN DOM RENDERING CONTAINER FOR BULK ZIP EXPORT -->
    <div class="fixed top-[-9999px] left-[-9999px] space-y-10">
        <template x-for="st in getSelectedForPrint()" :key="'export-' + st.id">
            <div class="space-y-4">
                <!-- EXPORT FRONT PNG CONTAINER -->
                <div :id="'card-front-export-' + st.id" class="w-[260px] h-[450px] bg-white border border-slate-300 flex text-slate-900 font-sans">
                    <div class="w-[52px] bg-[#373887] text-white flex flex-col items-center justify-center py-6 relative overflow-hidden shrink-0">
                        <div class="rotate-[-90deg] whitespace-nowrap tracking-[0.45em] font-black text-[13px] uppercase text-white">
                            UVAS SWAT
                        </div>
                    </div>
                    <div class="flex-1 p-4 flex flex-col items-center justify-between text-center space-y-2 bg-white">
                        <div class="flex flex-col items-center">
                            <img src="{{ asset('images/uvas_official_logo.png') }}" alt="UVAS Logo" class="w-16 h-16 object-contain">
                        </div>
                        <div class="w-24 h-24 rounded-full border-4 border-slate-200 overflow-hidden bg-slate-100 flex items-center justify-center text-slate-400 text-3xl font-bold">
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
                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://uvasswat.edu.pk/verify?reg=' + st.reg" alt="QR Code" class="w-12 h-12 object-contain bg-white p-1 border rounded">
                            <span class="font-mono font-black text-[10px] text-slate-900 uppercase" x-text="'CARD ID: ' + st.reg"></span>
                        </div>
                    </div>
                </div>

                <!-- EXPORT BACK PNG CONTAINER -->
                <div :id="'card-back-export-' + st.id" class="w-[260px] h-[450px] bg-white border border-slate-300 flex flex-col justify-between text-slate-900 font-sans">
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
                                        <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://uvasswat.edu.pk/verify?reg=' + st.reg" alt="QR Code" class="w-12 h-12 object-contain bg-white p-1 border rounded">
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
