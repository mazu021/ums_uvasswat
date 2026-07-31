@extends('layouts.app')

@section('title', 'Payroll & Payslips')
@section('header_title', 'Monthly Payroll & Salary Disbursement')

@section('content')
<div class="space-y-6" x-data="{ generateModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Monthly Payroll Management</h3>
            <p class="text-xs text-slate-500">Calculate basic salary, housing/medical allowances, tax deductions, and generate payslips.</p>
        </div>
        <button @click="generateModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>Process Monthly Payslip</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <form method="GET" action="{{ route('hr.payroll.index') }}" class="flex items-center space-x-3 text-xs font-bold text-slate-700">
            <span>Filter Period:</span>
            <select name="month" class="px-3 py-1.5 border rounded-lg">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                @endfor
            </select>
            <select name="year" class="px-3 py-1.5 border rounded-lg">
                <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
                <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025</option>
            </select>
            <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg">Filter</button>
        </form>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Payslip No.</th>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Period</th>
                        <th class="px-6 py-3">Basic Salary</th>
                        <th class="px-6 py-3">Allowances</th>
                        <th class="px-6 py-3">Deductions</th>
                        <th class="px-6 py-3">Net Salary</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payrolls as $pay)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $pay->payslip_number }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $pay->employee->full_name }}
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $pay->employee->designation }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ date('M', mktime(0, 0, 0, $pay->month, 10)) }} {{ $pay->year }}</td>
                            <td class="px-6 py-4">Rs. {{ number_format($pay->basic_salary, 2) }}</td>
                            <td class="px-6 py-4 text-emerald-600">+Rs. {{ number_format($pay->allowances, 2) }}</td>
                            <td class="px-6 py-4 text-red-500">-Rs. {{ number_format($pay->deductions, 2) }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">Rs. {{ number_format($pay->net_salary, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Paid</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('hr.payroll.payslip', $pay->id) }}" target="_blank" class="px-3 py-1 bg-slate-800 text-white font-bold rounded-lg text-[10px]">
                                    <i class="fa-solid fa-print me-1"></i> Print Payslip
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-6 text-center text-slate-400">No payroll records for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-slate-50">
            {{ $payrolls->links() }}
        </div>
    </div>

    <!-- Generate Payslip Modal -->
    <div x-show="generateModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="generateModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Process Employee Payslip</h4>
                <button @click="generateModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('hr.payroll.generate') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select Employee</label>
                    <select name="employee_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} (Basic: Rs. {{ number_format($emp->basic_salary) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Month</label>
                        <select name="month" class="w-full px-3 py-2 border rounded-lg">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Year</label>
                        <input type="number" name="year" value="2026" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Allowances (HRA/Medical)</label>
                        <input type="number" step="0.01" name="allowances" placeholder="0.00" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Deductions (Tax/Fund)</label>
                        <input type="number" step="0.01" name="deductions" placeholder="0.00" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="generateModal = false" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white font-bold rounded-lg shadow">Generate Payslip</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
