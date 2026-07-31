<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#212370',
                            900: '#212370',
                            950: '#191b5c',
                        },
                        emerald: {
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome & Alpine JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased bg-slate-50" x-data="layoutApp()">
    <div class="min-h-screen flex bg-slate-50">

        <!-- Mobile Backdrop -->
        <div x-show="!sidebarCollapsed" 
             @click="sidebarCollapsed = true" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs"
             style="display: none;"></div>

        <!-- Sidebar Navigation (Clean White Theme) -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white text-slate-700 transition-all duration-300 ease-in-out flex flex-col border-r border-slate-200 shadow-sm"
               :class="sidebarCollapsed ? '-translate-x-full' : 'translate-x-0'">
            <div class="h-20 flex items-center justify-between px-5 bg-white border-b border-slate-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center p-1 border border-slate-200 shadow-xs">
                        <img src="{{ asset('images/uvas_logo.png') }}" alt="UVAS Swat" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-slate-900 font-extrabold text-base leading-tight tracking-wide">UVAS SWAT</h1>
                        <p class="text-emerald-600 text-xs font-semibold">UMS Portal</p>
                    </div>
                </div>
                <!-- Mobile Hide Toggle -->
                <button @click="sidebarCollapsed = true" class="lg:hidden text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Navigation Links (Scrollbar hidden) -->
            <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-6 no-scrollbar">

                @if(Auth::user()->hasRole('Student'))
                <!-- Student Portal Menu -->
                <div>
                    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Student Portal</p>
                    <div class="space-y-1">
                        <a href="{{ route('student.dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('student.dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-xs border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-gauge-high w-6 text-center {{ request()->routeIs('student.dashboard') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('student.courses') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('student.courses') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-book-open w-6 text-center {{ request()->routeIs('student.courses') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Enrolled Courses</span>
                        </a>
                        <a href="{{ route('attendance.student.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('attendance.student.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-calendar-check w-6 text-center {{ request()->routeIs('attendance.student.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Attendance Record</span>
                        </a>
                        <!-- Examination & Transcripts Dropdown -->
                        <div x-data="{ openExams: {{ request()->routeIs('student.exams') || request()->routeIs('student.transcript') ? 'true' : 'false' }} }" class="space-y-1">
                            <button @click="openExams = !openExams" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('student.exams') || request()->routeIs('student.transcript') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-award w-6 text-center {{ request()->routeIs('student.exams') || request()->routeIs('student.transcript') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                                    <span>Exams & Transcripts</span>
                                </div>
                                <i class="fa-solid text-[10px] transition-transform duration-200" :class="openExams ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                            </button>
                            <div x-show="openExams" class="pl-8 space-y-1 pt-1">
                                <a href="{{ route('student.exams') }}" class="flex items-center space-x-2 px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ request()->routeIs('student.exams') ? 'text-emerald-700 font-extrabold bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    <i class="fa-solid fa-table-list text-[10px]"></i>
                                    <span>Exam Marks & Grades</span>
                                </a>
                                <a href="{{ route('student.transcript') }}" class="flex items-center space-x-2 px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ request()->routeIs('student.transcript') ? 'text-emerald-700 font-extrabold bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    <i class="fa-solid fa-file-pdf text-[10px]"></i>
                                    <span>Download Transcript</span>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('student.fees') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('student.fees') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-file-invoice-dollar w-6 text-center {{ request()->routeIs('student.fees') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Fee Challans & Proof</span>
                        </a>
                        <a href="{{ route('announcements.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('announcements.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-bullhorn w-6 text-center {{ request()->routeIs('announcements.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Announcements & Alerts</span>
                        </a>
                        <a href="{{ route('academics.calendar.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('academics.calendar.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-calendar-days w-6 text-center {{ request()->routeIs('academics.calendar.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Academic Calendar</span>
                        </a>
                    </div>
                </div>

                @elseif(Auth::user()->hasRole('Faculty') || Auth::user()->hasRole('Teacher'))
                <!-- Faculty Portal Menu (EXCLUSIVELY FACULTY FEATURES) -->
                <div>
                    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Faculty Portal</p>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-xs border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-gauge-high w-6 text-center {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Faculty Dashboard</span>
                        </a>
                        <a href="{{ route('attendance.teacher.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('attendance.teacher.*') || request()->routeIs('attendance.mark.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-layer-group w-6 text-center {{ request()->routeIs('attendance.teacher.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Assigned Courses & Classes</span>
                        </a>
                        <a href="{{ route('attendance.teacher.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('attendance.mark.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-clipboard-user w-6 text-center {{ request()->routeIs('attendance.mark.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Take Attendance</span>
                        </a>
                        <a href="{{ route('academics.exams.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('academics.exams.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-award w-6 text-center {{ request()->routeIs('academics.exams.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Exam Marks & Grades</span>
                        </a>
                        <a href="{{ route('hr.leaves.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('hr.leaves.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-umbrella-beach w-6 text-center {{ request()->routeIs('hr.leaves.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Apply for Leave</span>
                        </a>
                        <a href="{{ route('attendance.reports.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('attendance.reports.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-chart-pie w-6 text-center {{ request()->routeIs('attendance.reports.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Attendance Reports</span>
                        </a>
                        <a href="{{ route('announcements.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('announcements.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-bullhorn w-6 text-center {{ request()->routeIs('announcements.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Campus Announcements</span>
                        </a>
                        <a href="{{ route('academics.calendar.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('academics.calendar.*') ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-calendar-days w-6 text-center {{ request()->routeIs('academics.calendar.*') ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                            <span>Academic Calendar</span>
                        </a>
                    </div>
                </div>

                @else
                <!-- Admin / Main Menu (Complete 12-Module Tree Architecture) -->
                <div class="space-y-2">

                    <!-- 1. Dashboard -->
                    <div>
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-xs font-bold rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 font-extrabold border-l-4 border-emerald-600 shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-house w-6 text-center text-emerald-600"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>

                    <!-- 2. Academic Management -->
                    <div x-data="{ open: {{ request()->routeIs('academics.*') || request()->routeIs('course-offerings.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-graduation-cap w-6 text-center text-indigo-600"></i>
                                <span>Academic Management</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('academics.sessions.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('academics.sessions.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Academic Sessions</a>
                            <a href="{{ route('academics.programs.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('academics.programs.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Programs</a>
                            <a href="{{ route('hr.departments.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('hr.departments.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Departments</a>
                            <a href="{{ route('academics.courses.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('academics.courses.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Courses & Catalog</a>
                            <a href="{{ route('academics.curriculum.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('academics.curriculum.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Subjects / Curriculum</a>
                            <a href="{{ route('academics.sections.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('academics.sections.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Sections</a>
                            <a href="{{ route('academics.timetable.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('academics.timetable.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Timetable</a>
                            <a href="{{ route('attendance.reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('attendance.reports.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Attendance</a>
                            <a href="{{ route('academics.exams.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('academics.exams.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Examinations</a>
                            <a href="{{ route('academics.results.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('academics.results.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Results</a>
                            <a href="{{ route('academics.calendar.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg {{ request()->routeIs('academics.calendar.*') ? 'text-indigo-700 font-extrabold bg-indigo-50' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Academic Calendar</a>
                        </div>
                    </div>

                    <!-- 3. Student Management -->
                    <div x-data="{ open: {{ request()->routeIs('academics.students.*') || request()->routeIs('admissions.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-user-graduate w-6 text-center text-blue-600"></i>
                                <span>Student Management</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('admissions.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Student Admission</a>
                            <a href="{{ route('academics.students.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Student List</a>
                            <a href="{{ route('academics.students.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Student Promotion</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Student Transfer & Migration']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Student Transfer</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Student Documents Repository']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Student Documents</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Student ID Cards Generator']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Student ID Cards</a>
                            <a href="{{ route('attendance.reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Student Attendance</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Alumni Portal & Network']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Alumni</a>
                        </div>
                    </div>

                    <!-- 4. Faculty & Staff -->
                    <div x-data="{ open: {{ request()->routeIs('hr.employees.*') || request()->routeIs('hr.attendance.*') || request()->routeIs('hr.leaves.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-user-tie w-6 text-center text-purple-600"></i>
                                <span>Faculty & Staff</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('hr.employees.index', ['type' => 'faculty']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Faculty Members</a>
                            <a href="{{ route('hr.employees.index', ['type' => 'staff']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Administrative Staff</a>
                            <a href="{{ route('hr.departments.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Departments</a>
                            <a href="{{ route('hr.departments.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Designations</a>
                            <a href="{{ route('hr.attendance.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Attendance</a>
                            <a href="{{ route('hr.leaves.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Leave Management</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Faculty & Staff Performance Evaluation']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Performance Evaluation</a>
                        </div>
                    </div>

                    <!-- 5. Finance -->
                    <div x-data="{ open: {{ request()->routeIs('finance.*') || request()->routeIs('hr.payroll.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-coins w-6 text-center text-amber-600"></i>
                                <span>Finance</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('finance.fees.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Fee Structure</a>
                            <a href="{{ route('finance.fees.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Fee Collection</a>
                            <a href="{{ route('finance.scholarships.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Scholarships</a>
                            <a href="{{ route('finance.accounts.index', ['type' => 'debit']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Expenses</a>
                            <a href="{{ route('finance.accounts.index', ['type' => 'credit']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Income</a>
                            <a href="{{ route('hr.payroll.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Payroll</a>
                            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Financial Reports</a>
                        </div>
                    </div>

                    <!-- 6. Library -->
                    <div x-data="{ open: {{ request()->routeIs('services.library') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-book-bookmark w-6 text-center text-teal-600"></i>
                                <span>Library</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('services.library') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Books Catalog</a>
                            <a href="{{ route('services.library') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Categories</a>
                            <a href="{{ route('services.library') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Issue Books</a>
                            <a href="{{ route('services.library') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Return Books</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Library Fine Management']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Fine Management</a>
                            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Library Reports</a>
                        </div>
                    </div>

                    <!-- 7. Communication -->
                    <div x-data="{ open: {{ request()->routeIs('announcements.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-bullhorn w-6 text-center text-rose-600"></i>
                                <span>Communication</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('announcements.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Announcements</a>
                            <a href="{{ route('announcements.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Notices</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Email Broadcast Engine']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Email Broadcast</a>
                            <a href="{{ route('coming-soon', ['feature' => 'SMS Notification Gateway']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">SMS Gateway</a>
                            <a href="{{ route('announcements.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Notifications</a>
                        </div>
                    </div>

                    <!-- 8. Documents -->
                    <div x-data="{ open: {{ request()->routeIs('academics.transcript') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-file-contract w-6 text-center text-cyan-600"></i>
                                <span>Documents</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('coming-soon', ['feature' => 'Degrees & Certificates']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Certificates</a>
                            <a href="{{ route('academics.transcript') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Transcripts</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Official Letters Generator']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Letters</a>
                            <a href="{{ route('academics.students.download-sample') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Downloads</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Document Templates']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Templates</a>
                        </div>
                    </div>

                    <!-- 9. Administration -->
                    <div x-data="{ open: {{ request()->routeIs('services.hostel') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-building-columns w-6 text-center text-[#2e2e7f]"></i>
                                <span>Administration</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('coming-soon', ['feature' => 'Campus Management']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Campus</a>
                            <a href="{{ route('services.hostel') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Buildings</a>
                            <a href="{{ route('services.hostel') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Rooms & Facilities</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Official Holidays Calendar']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Holidays</a>
                            <a href="{{ route('announcements.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Events</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Visitor Management System']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Visitor Management</a>
                        </div>
                    </div>

                    <!-- 10. Reports -->
                    <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-chart-pie w-6 text-center text-emerald-600"></i>
                                <span>Reports</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Academic Reports</a>
                            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Student Reports</a>
                            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Finance Reports</a>
                            <a href="{{ route('attendance.reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Attendance Reports</a>
                            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Examination Reports</a>
                            <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Custom Reports</a>
                        </div>
                    </div>

                    <!-- 11. System Settings -->
                    <div x-data="{ open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('settings.*') || request()->routeIs('audit-logs.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-sliders w-6 text-center text-slate-600"></i>
                                <span>System Settings</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">General Settings</a>
                            <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Academic Settings</a>
                            <a href="{{ route('roles.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">User Roles</a>
                            <a href="{{ route('roles.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Permissions</a>
                            <a href="{{ route('users.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Users Account List</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Database Backup & Restore']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Backup & Restore</a>
                            <a href="{{ route('audit-logs.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Activity Logs</a>
                            <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">System Configuration</a>
                        </div>
                    </div>

                    <!-- 12. Help & Support -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl text-slate-700 hover:bg-slate-100 transition">
                            <div class="flex items-center">
                                <i class="fa-solid fa-circle-question w-6 text-center text-blue-500"></i>
                                <span>Help & Support</span>
                            </div>
                            <i class="fa-solid text-[9px] transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        <div x-show="open" class="pl-7 space-y-0.5 pt-1" style="display: none;">
                            <a href="{{ route('coming-soon', ['feature' => 'User Guide & Documentation']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">User Guide</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Frequently Asked Questions']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">FAQs</a>
                            <a href="{{ route('coming-soon', ['feature' => 'Contact Technical Support']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">Contact Support</a>
                            <a href="{{ route('coming-soon', ['feature' => 'About UMS ERP System']) }}" class="flex items-center px-3 py-1.5 text-[11px] font-semibold rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900">About System</a>
                        </div>
                    </div>

                </div>
                @endif
            </nav>

            <!-- User Footer Profile Box -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover shadow-xs border border-emerald-500">
                    @else
                        <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="truncate max-w-[130px]">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-emerald-600 font-semibold truncate">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="text-slate-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-slate-200/50">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 transition-all duration-300 ease-in-out flex flex-col min-w-0"
             :class="sidebarCollapsed ? 'pl-0' : 'lg:pl-64'">

            <!-- Top Navbar (Clean White Responsive Style) -->
            <header class="min-h-16 lg:h-20 bg-white border-b border-slate-200 sticky top-0 z-40 flex items-center justify-between px-3 sm:px-6 lg:px-8 py-2 shadow-xs">
                <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4 min-w-0">
                    <!-- Sidebar Toggle 3-Lines Button -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed" 
                            class="p-2 sm:p-2.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition focus:outline-none flex items-center justify-center shrink-0"
                            title="Toggle Sidebar">
                        <i class="fa-solid fa-bars text-lg sm:text-xl"></i>
                    </button>

                    <div class="min-w-0">
                        <h2 class="text-sm sm:text-base lg:text-xl font-bold text-slate-800 leading-tight truncate">
                            @yield('header_title', 'University Management System')
                        </h2>
                        <p class="text-[11px] lg:text-xs text-slate-500 hidden sm:block">The University of Veterinary and Animal Sciences, Swat (UVAS Swat)</p>
                        <p class="text-[9px] text-slate-400 block sm:hidden font-medium">UVAS Swat UMS</p>
                    </div>
                </div>

                <!-- Top Navbar Actions -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    
                    <!-- Full Screen Toggle Button -->
                    <button @click="toggleFullScreen()" 
                            class="p-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition focus:outline-none"
                            title="Toggle Full Screen">
                        <i class="fa-solid" :class="isFullscreen ? 'fa-compress text-lg' : 'fa-expand text-lg'"></i>
                    </button>

                    <!-- Notification Bell -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2.5 text-slate-500 hover:text-emerald-600 hover:bg-slate-100 rounded-xl transition focus:outline-none">
                            <i class="fa-regular fa-bell text-lg"></i>
                            <span class="absolute top-2 right-2 w-2 h-2 bg-emerald-500 rounded-full ring-2 ring-white"></span>
                        </button>
                        <!-- Notification Dropdown -->
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 z-50" style="display: none;">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
                                <h4 class="font-bold text-sm text-slate-800">System Notifications</h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">New</span>
                            </div>
                            <div class="space-y-3 text-xs">
                                <div class="p-2.5 bg-emerald-50/70 rounded-xl border-l-3 border-emerald-500">
                                    <p class="font-bold text-slate-800">Fall 2026 Academic Session Active</p>
                                    <p class="text-slate-500 mt-0.5">Registration open for all undergraduate programs.</p>
                                </div>
                                <div class="p-2.5 bg-slate-50 rounded-xl border-l-3 border-slate-400">
                                    <p class="font-bold text-slate-800">Monthly Payroll Generated</p>
                                    <p class="text-slate-500 mt-0.5">Payslips for current month are ready to view.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="h-6 w-px bg-slate-200 hidden sm:block mx-1"></div>

                    <!-- Profile Dropdown (Top Right Corner) -->
                    <div class="relative" x-data="{ dropdownOpen: false }">
                        <button @click="dropdownOpen = !dropdownOpen" 
                                class="flex items-center space-x-2.5 p-1.5 rounded-full hover:bg-slate-100 transition border border-slate-200 bg-white shadow-2xs pr-3">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover shadow-xs border border-emerald-500">
                            @else
                                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div class="hidden sm:flex flex-col text-left">
                                <span class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-slate-500 font-medium">{{ Auth::user()->getRoleNames()->first() ?? 'Member' }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="dropdownOpen" 
                             @click.away="dropdownOpen = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 overflow-hidden"
                             style="display: none;">
                            
                            <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Signed in as</p>
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->email ?? Auth::user()->name }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded-md">
                                    {{ Auth::user()->getRoleNames()->first() ?? 'Member' }}
                                </span>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition">
                                    <i class="fa-regular fa-user w-5 text-center me-2 text-slate-400"></i>
                                    <span>View Profile</span>
                                </a>
                                <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition">
                                    <i class="fa-solid fa-sliders w-5 text-center me-2 text-slate-400"></i>
                                    <span>Settings</span>
                                </a>
                            </div>

                            <div class="border-t border-slate-100 pt-1">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                        <i class="fa-solid fa-right-from-bracket w-5 text-center me-2 text-red-500"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Alerts Container -->
            <div class="px-4 lg:px-8 mt-4">
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-900 font-medium text-sm flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-900 font-medium text-sm flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle-exclamation text-red-600 text-lg"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-900 text-sm shadow-xs space-y-1">
                        <p class="font-bold flex items-center space-x-2">
                            <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                            <span>Validation Error:</span>
                        </p>
                        <ul class="list-disc list-inside pl-4 text-xs space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-8">
                @yield('content')
            </main>

            <!-- Institutional Footer -->
            <footer class="bg-white border-t border-slate-200 py-4 px-8 text-center text-xs text-slate-500">
                <p>
                    <a href="https://www.linkedin.com/in/mazu021/" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-700 hover:underline transition-colors font-medium">
                        &copy; UMS PORTAL The University of Veterinary and Animal Sciences, Swat (UVAS Swat) - Directorate of IT
                    </a>
                </p>
            </footer>
        </div>
    </div>

    <script>
        function layoutApp() {
            return {
                sidebarCollapsed: window.innerWidth < 1024,
                isFullscreen: false,
                init() {
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = !!document.fullscreenElement;
                    });
                    window.addEventListener('resize', () => {
                        if (window.innerWidth < 1024) {
                            this.sidebarCollapsed = true;
                        }
                    });
                },
                toggleFullScreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            console.warn("Fullscreen request error:", err);
                        });
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen().catch(err => {
                                console.warn("Exit fullscreen error:", err);
                            });
                        }
                    }
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>

