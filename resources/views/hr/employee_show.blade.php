@extends('layouts.app')

@section('title', $employee->full_name . ' - Profile')
@section('header_title', 'Employee Profile')

@section('content')
<div class="space-y-6">

    <!-- Profile Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white font-bold flex items-center justify-center text-2xl shadow-lg">
                {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $employee->full_name }}</h2>
                <p class="text-xs font-semibold text-emerald-700">{{ $employee->designation }} • {{ $employee->department->name }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Code: {{ $employee->employee_code }} | CNIC: {{ $employee->cnic ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">
                Status: {{ ucfirst($employee->status) }}
            </span>
        </div>
    </div>

    <!-- Tabs Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
        <!-- Key Employee Info -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="font-bold text-sm text-slate-800 border-b pb-2">Employment Information</h4>
            <div class="space-y-2">
                <div class="flex justify-between py-1 border-b border-slate-50"><span class="text-slate-400">Email:</span> <span class="font-bold text-slate-800">{{ $employee->email }}</span></div>
                <div class="flex justify-between py-1 border-b border-slate-50"><span class="text-slate-400">Phone:</span> <span class="font-bold text-slate-800">{{ $employee->phone ?? 'N/A' }}</span></div>
                <div class="flex justify-between py-1 border-b border-slate-50"><span class="text-slate-400">Staff Type:</span> <span class="font-bold text-emerald-700 uppercase">{{ $employee->type }}</span></div>
                <div class="flex justify-between py-1 border-b border-slate-50"><span class="text-slate-400">Basic Salary:</span> <span class="font-bold text-slate-900">Rs. {{ number_format($employee->basic_salary, 2) }}</span></div>
                <div class="flex justify-between py-1"><span class="text-slate-400">Joined Date:</span> <span class="font-bold text-slate-800">{{ $employee->joining_date ? $employee->joining_date->format('M d, Y') : 'N/A' }}</span></div>
            </div>
        </div>

        <!-- Attendance History -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <h4 class="font-bold text-sm text-slate-800 border-b pb-2">Recent Attendance Log</h4>
            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                @forelse($employee->attendances as $att)
                    <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg">
                        <span>{{ $att->date->format('M d, Y') }}</span>
                        <span class="font-bold px-2 py-0.5 rounded text-[10px] {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ ucfirst($att->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 text-center py-4">No attendance records.</p>
                @endforelse
            </div>
        </div>

        <!-- Payslips & Leave History -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <h4 class="font-bold text-sm text-slate-800 border-b pb-2">Payroll & Payslips</h4>
            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                @forelse($employee->payrolls as $pay)
                    <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg">
                        <div>
                            <p class="font-bold text-slate-800">{{ $pay->payslip_number }}</p>
                            <p class="text-[10px] text-slate-400">{{ date('F', mktime(0, 0, 0, $pay->month, 10)) }} {{ $pay->year }}</p>
                        </div>
                        <a href="{{ route('hr.payroll.payslip', $pay->id) }}" target="_blank" class="px-2.5 py-1 bg-emerald-600 text-white font-bold rounded-lg text-[10px]">
                            Payslip
                        </a>
                    </div>
                @empty
                    <p class="text-slate-400 text-center py-4">No payslips generated.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
