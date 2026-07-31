@extends('layouts.app')

@section('title', 'Finance & Student Fee Collection')
@section('header_title', 'Finance & Student Fee Management')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'verification', showConfigModal: false, showVerifyModal: false, verifyChallan: null, showEditStructureModal: false, editStructure: null }">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-navy-900 to-indigo-950 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="px-2.5 py-0.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[11px] font-bold rounded-full">
                    <i class="fa-solid fa-vault me-1"></i> Directorate of Finance & Accounts
                </span>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">Student Fee Collection & Verification</h1>
            <p class="text-slate-300 text-xs mt-1">
                Configure session-wise program/department fee structures, fine rules, batch issue semester challans, and verify or edit student payment slips.
            </p>
        </div>

        <div class="flex items-center space-x-3 shrink-0">
            <button @click="showConfigModal = true" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl shadow-lg transition flex items-center space-x-2">
                <i class="fa-solid fa-plus-circle text-sm"></i>
                <span>Configure Program/Dept Fee & Fine</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 space-x-4">
        <button @click="activeTab = 'verification'" 
                :class="activeTab === 'verification' ? 'border-emerald-600 text-emerald-700 font-extrabold border-b-2' : 'text-slate-500 hover:text-slate-800 font-semibold'" 
                class="pb-3 px-2 text-sm transition flex items-center space-x-2">
            <i class="fa-solid fa-clipboard-check"></i>
            <span>Student Payment Proof Verifications ({{ $challans->total() }})</span>
        </button>

        <button @click="activeTab = 'structures'" 
                :class="activeTab === 'structures' ? 'border-emerald-600 text-emerald-700 font-extrabold border-b-2' : 'text-slate-500 hover:text-slate-800 font-semibold'" 
                class="pb-3 px-2 text-sm transition flex items-center space-x-2">
            <i class="fa-solid fa-list-check"></i>
            <span>Academic Session Fee Structures ({{ $feeStructures->count() }})</span>
        </button>

        <button @click="activeTab = 'batch'" 
                :class="activeTab === 'batch' ? 'border-emerald-600 text-emerald-700 font-extrabold border-b-2' : 'text-slate-500 hover:text-slate-800 font-semibold'" 
                class="pb-3 px-2 text-sm transition flex items-center space-x-2">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span>Batch Issue Semester Fee Challans</span>
        </button>
    </div>

    <!-- TAB 1: Student Payment Verification Hub -->
    <div x-show="activeTab === 'verification'" class="space-y-4">
        
        <!-- Filter & Search Bar -->
        <form action="{{ route('finance.fees.index') }}" method="GET" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Status Filter</label>
                <select name="status" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Statuses</option>
                    <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid & Verified</option>
                    <option value="rejected_reupload" {{ request('status') === 'rejected_reupload' ? 'selected' : '' }}>Rejected (Re-upload Requested)</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Program</label>
                <select name="program_id" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Programs</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Department</label>
                <select name="department_id" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Search Student / Challan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, reg no, challan..." class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                    Filter Records
                </button>
                <a href="{{ route('finance.fees.index') }}" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>

        <!-- Challans Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="p-3 w-10 text-center">#</th>
                            <th class="p-3">Challan No</th>
                            <th class="p-3">Student Details</th>
                            <th class="p-3">Program & Department</th>
                            <th class="p-3 text-right">Amount Payable</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Proof Slip</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($challans as $index => $c)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3 text-center font-bold text-slate-400">{{ $challans->firstItem() + $index }}</td>
                                <td class="p-3 font-mono font-bold text-slate-900">
                                    {{ $c->challan_number }}
                                    <span class="block text-[10px] font-semibold text-slate-400">Due: {{ $c->due_date ? $c->due_date->format('d-M-Y') : 'N/A' }}</span>
                                </td>
                                <td class="p-3 font-bold text-slate-900">
                                    {{ $c->student->full_name ?? 'N/A' }}
                                    <span class="block text-[10px] font-mono font-bold text-slate-500">{{ $c->student->registration_number ?? ($c->student->roll_number ?? 'N/A') }}</span>
                                </td>
                                <td class="p-3 font-semibold text-slate-700">
                                    {{ $c->student->program->name ?? ($c->student->department->name ?? 'N/A') }}
                                </td>
                                <td class="p-3 text-right font-mono font-extrabold text-slate-900">
                                    Rs. {{ number_format($c->total_amount + ($c->late_fine_amount ?? 0), 2) }}
                                    @if(($c->late_fine_amount ?? 0) > 0)
                                        <span class="block text-[10px] text-rose-600 font-bold">+ Fine: Rs. {{ number_format($c->late_fine_amount, 0) }}</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($c->status === 'paid')
                                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded-full">
                                            PAID & VERIFIED
                                        </span>
                                    @elseif($c->status === 'pending_verification')
                                        <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 font-extrabold text-[10px] rounded-full animate-pulse">
                                            PENDING VERIFICATION
                                        </span>
                                    @elseif($c->status === 'rejected_reupload')
                                        <span class="px-2.5 py-0.5 bg-rose-100 text-rose-800 font-extrabold text-[10px] rounded-full">
                                            REJECTED (RE-UPLOAD)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 font-bold text-[10px] rounded-full">
                                            UNPAID
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($c->payment_proof)
                                        <button @click="verifyChallan = {{ json_encode($c) }}; showVerifyModal = true" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-lg border border-indigo-200 transition flex items-center space-x-1 mx-auto">
                                            <i class="fa-solid fa-eye"></i>
                                            <span>Inspect Slip</span>
                                        </button>
                                    @else
                                        <span class="text-[10px] text-slate-400 font-medium italic">No slip uploaded</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('finance.fees.challans.print', $c->id) }}" target="_blank" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] rounded-md transition" title="Print Bank Voucher">
                                            <i class="fa-solid fa-print me-1"></i> Print
                                        </a>
                                        <button @click="verifyChallan = {{ json_encode($c) }}; showVerifyModal = true" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] rounded-md transition flex items-center gap-1">
                                            <i class="fa-solid fa-pen-to-square"></i> Review / Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-400 font-medium">
                                    No student fee challan records found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                {{ $challans->links() }}
            </div>
        </div>

    </div>

    <!-- TAB 2: Session-Wise Program Fee Structures -->
    <div x-show="activeTab === 'structures'" class="space-y-6" style="display: none;">
        
        <!-- Filter Controls for Fee Structures -->
        <form action="{{ route('finance.fees.index') }}" method="GET" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <input type="hidden" name="active_tab" value="structures">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Academic Session Filter</label>
                <select name="filter_session_id" onchange="this.form.submit()" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Academic Sessions</option>
                    @foreach($academicSessions as $s)
                        <option value="{{ $s->id }}" {{ request('filter_session_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Degree Program</label>
                <select name="filter_prog_id" onchange="this.form.submit()" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Programs</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ request('filter_prog_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Department</label>
                <select name="filter_dept_id" onchange="this.form.submit()" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('filter_dept_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                    Filter Structures
                </button>
                <a href="{{ route('finance.fees.index') }}" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>

        @php
            $groupedStructures = $feeStructures->groupBy(function($item) {
                return $item->academicSession ? $item->academicSession->name : 'General / Unassigned Session';
            });
        @endphp

        @forelse($groupedStructures as $sessionName => $structures)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-4 gap-2">
                    <div class="flex items-center space-x-3">
                        <span class="px-3.5 py-1.5 bg-slate-900 text-amber-400 font-black text-xs rounded-xl shadow-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-days text-amber-400"></i>
                            <span>{{ $sessionName }}</span>
                        </span>
                        <span class="text-xs text-slate-500 font-extrabold bg-slate-100 px-2.5 py-1 rounded-lg">
                            {{ count($structures) }} Program Fee Structures
                        </span>
                    </div>
                    <button @click="showConfigModal = true" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-plus-circle text-xs"></i>
                        <span>Add Fee Structure</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100/90 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase">
                                <th class="p-3">Program / Scope</th>
                                <th class="p-3 text-center">Semester</th>
                                <th class="p-3 text-right">Tuition Fee</th>
                                <th class="p-3 text-right">Admission/Reg</th>
                                <th class="p-3 text-right">Exam & Lab</th>
                                <th class="p-3 text-right text-rose-600 font-extrabold">Late Fee Fine</th>
                                <th class="p-3 text-right font-black text-slate-900">Total Amount</th>
                                <th class="p-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($structures as $fs)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-3 font-bold text-slate-900">
                                        @if($fs->program)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] rounded font-mono me-1">PROGRAM</span>
                                            {{ $fs->program->name }}
                                        @elseif($fs->department)
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] rounded font-mono me-1">DEPARTMENT</span>
                                            Department of {{ $fs->department->name }}
                                        @else
                                            <span class="px-2 py-0.5 bg-slate-900 text-white text-[10px] rounded font-mono me-1">GLOBAL</span>
                                            All Departments
                                        @endif
                                    </td>
                                    <td class="p-3 text-center font-bold text-slate-700">
                                        @if(!$fs->semester || $fs->semester == 0)
                                            @php
                                                $maxSem = $fs->program ? ($fs->program->total_semesters ?? 8) : 8;
                                            @endphp
                                            <span class="px-2.5 py-1 bg-indigo-100 text-indigo-900 text-[10px] font-extrabold rounded-lg border border-indigo-200 shadow-xs inline-flex items-center gap-1">
                                                <i class="fa-solid fa-layer-group text-indigo-600"></i> All Semesters (1 - {{ $maxSem }})
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-800 text-[11px] font-bold rounded">
                                                Sem {{ $fs->semester }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right font-mono font-bold text-emerald-800">Rs. {{ number_format($fs->tuition_fee, 2) }}</td>
                                    <td class="p-3 text-right font-mono">Rs. {{ number_format($fs->admission_fee, 2) }}</td>
                                    <td class="p-3 text-right font-mono">Rs. {{ number_format($fs->examination_fee + $fs->library_fee, 2) }}</td>
                                    <td class="p-3 text-right font-mono text-rose-600 font-extrabold">Rs. {{ number_format($fs->late_fee_fine ?? 0, 2) }}</td>
                                    <td class="p-3 text-right font-mono font-black text-slate-900">Rs. {{ number_format($fs->total_amount, 2) }}</td>
                                    <td class="p-3 text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            <button @click="editStructure = {{ json_encode($fs) }}; showEditStructureModal = true" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[10px] rounded-md transition flex items-center gap-1">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>

                                            <form action="{{ route('finance.fees.structures.allocate', $fs->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-[10px] rounded-md transition" title="Auto Allocate Fee Challans to Enrolled Students">
                                                    <i class="fa-solid fa-users-gear me-1"></i> Allocate
                                                </button>
                                            </form>

                                            <form action="{{ route('finance.fees.structures.destroy', $fs->id) }}" method="POST" onsubmit="return confirm('Delete this fee structure configuration?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[10px] rounded-md transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 text-center rounded-2xl border border-slate-200 text-slate-400">
                <i class="fa-solid fa-folder-open text-4xl block mb-2 text-slate-300"></i>
                No fee structures configured for the selected filters.
            </div>
        @endforelse
    </div>

    <!-- TAB 3: Batch Issue Semester Fee Challans -->
    <div x-show="activeTab === 'batch'" class="space-y-4" style="display: none;">
        <form action="{{ route('finance.fees.generate-batch') }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm max-w-2xl mx-auto space-y-4">
            @csrf
            <div class="border-b pb-3">
                <h3 class="font-bold text-base text-slate-900">Batch Generate Semester Fee Challans</h3>
                <p class="text-xs text-slate-500">Issue fee challans automatically for all enrolled students in a program or department.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Student Scope *</label>
                <select name="scope" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                    <option value="program">Specific Degree Program</option>
                    <option value="department">Entire Department</option>
                    <option value="all">All Enrolled Students (University-wide)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Select Program</label>
                    <select name="program_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        <option value="">Choose Program...</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Select Department</label>
                    <select name="department_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        <option value="">Choose Department...</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Semester Number *</label>
                    <input type="number" name="semester" required min="1" max="10" value="1" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Fee Payment Due Date *</label>
                    <input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>Generate & Issue Student Fee Challans</span>
            </button>
        </form>
    </div>

    <!-- MODAL 1: Configure Program/Department Fee & Fine Structure -->
    <div x-show="showConfigModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="showConfigModal = false" class="w-full max-w-lg bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-base text-slate-900">Configure Session Fee Structure</h3>
                <button @click="showConfigModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('finance.fees.structures.store') }}" method="POST" class="space-y-3" x-data="{ scope: 'program' }">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Academic Session *</label>
                    <select name="academic_session_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        <option value="">Select Academic Session...</option>
                        @foreach($academicSessions as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Apply Fee To Scope *</label>
                    <select name="apply_scope" x-model="scope" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        <option value="program">Specific Degree Program</option>
                        <option value="department">Entire Department</option>
                        <option value="all_programs">All Degree Programs (All Programs at Once)</option>
                        <option value="all_departments">All Departments Globally</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="scope === 'program' || scope === 'department'">
                    <div x-show="scope === 'program'">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Degree Program *</label>
                        <select name="program_id" :required="scope === 'program'" @change="if($event.target.value === 'all') scope = 'all_programs'" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                            <option value="">Select Program...</option>
                            <option value="all" class="font-extrabold text-indigo-700 bg-indigo-50">-- All Degree Programs --</option>
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="scope === 'department'">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Department *</label>
                        <select name="department_id" :required="scope === 'department'" @change="if($event.target.value === 'all') scope = 'all_departments'" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                            <option value="">Select Department...</option>
                            <option value="all" class="font-extrabold text-blue-700 bg-blue-50">-- All Departments --</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div x-show="scope === 'all_programs'" class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600 text-sm"></i>
                    <span>Fee structure will be saved for ALL active degree programs in the selected session at once.</span>
                </div>

                <div x-show="scope === 'all_departments'" class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-globe text-blue-600 text-sm"></i>
                    <span>Fee structure will be applied globally to ALL departments in the selected session.</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Semester Scope *</label>
                        <select name="semester" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                            <option value="0" selected>All Semesters (Full Program Duration)</option>
                            <option value="1">Semester 1 Only</option>
                            <option value="2">Semester 2 Only</option>
                            <option value="3">Semester 3 Only</option>
                            <option value="4">Semester 4 Only</option>
                            <option value="5">Semester 5 Only</option>
                            <option value="6">Semester 6 Only</option>
                            <option value="7">Semester 7 Only</option>
                            <option value="8">Semester 8 Only</option>
                            <option value="9">Semester 9 Only</option>
                            <option value="10">Semester 10 Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tuition Fee *</label>
                        <input type="number" name="tuition_fee" required min="0" step="100" placeholder="45000" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Admission Fee</label>
                        <input type="number" name="admission_fee" min="0" step="100" placeholder="5000" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Exam Fee</label>
                        <input type="number" name="examination_fee" min="0" step="100" placeholder="3000" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Library & Lab</label>
                        <input type="number" name="library_fee" min="0" step="100" placeholder="2000" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-rose-700 uppercase mb-1">Late Fee Fine</label>
                        <input type="number" name="late_fee_fine" min="0" step="100" placeholder="500" class="w-full px-3 py-2 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-900">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition">
                    Save Fee Structure Configuration
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit Existing Fee Structure Modal -->
    <div x-show="showEditStructureModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="showEditStructureModal = false" class="w-full max-w-lg bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-base text-slate-900">Edit Program/Department Fee Structure</h3>
                <button @click="showEditStructureModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <template x-if="editStructure">
                <form action="{{ route('finance.fees.structures.update', 0) }}" :action="'/finance/fees/structures/' + editStructure.id" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Academic Session *</label>
                        <select name="academic_session_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                            @foreach($academicSessions as $s)
                                <option value="{{ $s->id }}" :selected="editStructure.academic_session_id == {{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Degree Program</label>
                            <select name="program_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                                <option value="">Select Program...</option>
                                <option value="all" class="font-extrabold text-indigo-700 bg-indigo-50">-- All Degree Programs --</option>
                                @foreach($programs as $p)
                                    <option value="{{ $p->id }}" :selected="editStructure.program_id == {{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Department</label>
                            <select name="department_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                                <option value="">Select Department...</option>
                                <option value="all" class="font-extrabold text-blue-700 bg-blue-50">-- All Departments --</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}" :selected="editStructure.department_id == {{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Semester Scope *</label>
                            <select name="semester" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                                <option value="0" :selected="editStructure.semester == 0 || !editStructure.semester">All Semesters (Full Program Duration)</option>
                                <option value="1" :selected="editStructure.semester == 1">Semester 1 Only</option>
                                <option value="2" :selected="editStructure.semester == 2">Semester 2 Only</option>
                                <option value="3" :selected="editStructure.semester == 3">Semester 3 Only</option>
                                <option value="4" :selected="editStructure.semester == 4">Semester 4 Only</option>
                                <option value="5" :selected="editStructure.semester == 5">Semester 5 Only</option>
                                <option value="6" :selected="editStructure.semester == 6">Semester 6 Only</option>
                                <option value="7" :selected="editStructure.semester == 7">Semester 7 Only</option>
                                <option value="8" :selected="editStructure.semester == 8">Semester 8 Only</option>
                                <option value="9" :selected="editStructure.semester == 9">Semester 9 Only</option>
                                <option value="10" :selected="editStructure.semester == 10">Semester 10 Only</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tuition Fee *</label>
                            <input type="number" name="tuition_fee" required min="0" step="100" :value="editStructure.tuition_fee" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Admission Fee</label>
                            <input type="number" name="admission_fee" min="0" step="100" :value="editStructure.admission_fee || 0" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Exam Fee</label>
                            <input type="number" name="examination_fee" min="0" step="100" :value="editStructure.examination_fee || 0" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Library & Lab</label>
                            <input type="number" name="library_fee" min="0" step="100" :value="editStructure.library_fee || 0" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-rose-700 uppercase mb-1">Late Fee Fine</label>
                            <input type="number" name="late_fee_fine" min="0" step="100" :value="editStructure.late_fee_fine || 0" class="w-full px-3 py-2 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-900">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-md transition">
                        Update & Save Fee Structure Details
                    </button>
                </form>
            </template>
        </div>
    </div>

    <!-- MODAL 3: Review, Verify, Edit & Reject Payment Proof Modal -->
    <div x-show="showVerifyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="showVerifyModal = false" class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="font-bold text-base text-slate-900">Review & Change Fee Challan Status</h3>
                    <p class="text-xs text-slate-500" x-text="verifyChallan ? 'Challan # ' + verifyChallan.challan_number + ' (Current Status: ' + verifyChallan.status.toUpperCase() + ')' : ''"></p>
                </div>
                <button @click="showVerifyModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <template x-if="verifyChallan">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Slip Image Preview -->
                        <div class="border border-slate-200 rounded-xl p-2 bg-slate-50 flex flex-col items-center justify-center min-h-[160px]">
                            <template x-if="verifyChallan.payment_proof">
                                <a :href="'/storage/' + verifyChallan.payment_proof" target="_blank">
                                    <img :src="'/storage/' + verifyChallan.payment_proof" alt="Payment Proof" class="max-h-64 object-contain rounded-lg border border-slate-200 shadow-xs">
                                </a>
                            </template>
                            <template x-if="!verifyChallan.payment_proof">
                                <div class="p-6 text-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-image text-3xl block mb-1"></i>
                                    No payment slip image uploaded.
                                </div>
                            </template>
                        </div>

                        <!-- Payment & Student Meta -->
                        <div class="space-y-2 text-xs">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Student Name</span>
                                <strong class="text-slate-900 font-bold text-sm" x-text="verifyChallan.student ? verifyChallan.student.first_name + ' ' + verifyChallan.student.last_name : 'N/A'"></strong>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Transaction Reference #</span>
                                <strong class="text-indigo-600 font-mono font-bold text-sm" x-text="verifyChallan.transaction_reference || 'N/A'"></strong>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Total Amount Payable</span>
                                <strong class="text-emerald-700 font-mono font-extrabold text-base" x-text="'Rs. ' + (parseFloat(verifyChallan.total_amount) + parseFloat(verifyChallan.late_fine_amount || 0)).toFixed(2)"></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Status Change Action Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                        <!-- Approve Form -->
                        <form action="{{ route('finance.fees.challans.verify', ['feeChallan' => 0]) }}" :action="'/finance/fees/challans/' + verifyChallan.id + '/verify'" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-circle-check text-sm"></i>
                                <span>Approve & Mark Paid</span>
                            </button>
                        </form>

                        <!-- Re-open / Mark Unpaid Form -->
                        <form action="{{ route('finance.fees.challans.verify', ['feeChallan' => 0]) }}" :action="'/finance/fees/challans/' + verifyChallan.id + '/verify'" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="reopen">
                            <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-rotate-left text-sm"></i>
                                <span>Re-open as Unpaid</span>
                            </button>
                        </form>
                    </div>

                    <!-- Reject Action Form (Re-upload Request) -->
                    <form action="{{ route('finance.fees.challans.verify', ['feeChallan' => 0]) }}" :action="'/finance/fees/challans/' + verifyChallan.id + '/verify'" method="POST" class="p-4 bg-rose-50 border border-rose-200 rounded-2xl space-y-2">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <label class="block text-xs font-bold text-rose-900 uppercase">Reject / Change to Re-upload Status</label>
                        <input type="text" name="rejection_reason" required placeholder="Specify reason (e.g. Accidental approval / Deposit slip invalid / Please re-upload clear receipt)..." class="w-full px-3 py-1.5 bg-white border border-rose-300 rounded-xl text-xs font-bold text-rose-900 focus:outline-none">
                        <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Reject & Prompt Re-upload
                        </button>
                    </form>

                    <!-- Edit Fee Challan Details Form -->
                    <form action="{{ route('finance.fees.challans.update', ['feeChallan' => 0]) }}" :action="'/finance/fees/challans/' + verifyChallan.id" method="POST" enctype="multipart/form-data" class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                        @csrf
                        @method('PUT')
                        <h4 class="font-extrabold text-xs text-slate-900 uppercase flex items-center gap-1">
                            <i class="fa-solid fa-pen-to-square text-indigo-600"></i> Edit Challan Amounts, Due Date & Upload Receipt Slip
                        </h4>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Total Fee (Rs.)</label>
                                <input type="number" name="total_amount" required min="0" step="100" :value="verifyChallan.total_amount" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Late Fine (Rs.)</label>
                                <input type="number" name="late_fine_amount" min="0" step="100" :value="verifyChallan.late_fine_amount || 0" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Status</label>
                                <select name="status" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                                    <option value="unpaid" :selected="verifyChallan.status === 'unpaid'">UNPAID</option>
                                    <option value="pending_verification" :selected="verifyChallan.status === 'pending_verification'">PENDING VERIFICATION</option>
                                    <option value="paid" :selected="verifyChallan.status === 'paid'">PAID & VERIFIED</option>
                                    <option value="rejected_reupload" :selected="verifyChallan.status === 'rejected_reupload'">REJECTED (RE-UPLOAD)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Transaction Ref # / Slip No</label>
                                <input type="text" name="transaction_reference" placeholder="e.g. HBL-98765432" :value="verifyChallan.transaction_reference || ''" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Upload Receipt Slip (Image / PDF)</label>
                                <input type="file" name="payment_proof" accept="image/*,.pdf" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-slate-200 file:text-slate-800">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Due Date</label>
                            <input type="date" name="due_date" required :value="verifyChallan.due_date ? verifyChallan.due_date.substring(0,10) : ''" class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900">
                        </div>

                        <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                            Save & Upload Updated Challan Details
                        </button>
                    </form>

                </div>
            </template>
        </div>
    </div>

</div>
@endsection
