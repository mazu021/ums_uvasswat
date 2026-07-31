@extends('layouts.app')

@section('title', 'Dynamic System Reports')
@section('header_title', 'Institutional Analytics & Reports')

@section('content')
<div class="space-y-6">

    <!-- Report Filter Tabs -->
    <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex space-x-2 text-xs font-bold">
            <a href="{{ route('reports.index', ['type' => 'financial']) }}" class="px-4 py-2 rounded-lg transition {{ $reportType === 'financial' ? 'bg-emerald-600 text-white shadow' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <i class="fa-solid fa-coins me-1"></i> Financial Report
            </a>
            <a href="{{ route('reports.index', ['type' => 'attendance']) }}" class="px-4 py-2 rounded-lg transition {{ $reportType === 'attendance' ? 'bg-emerald-600 text-white shadow' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <i class="fa-solid fa-calendar-check me-1"></i> Attendance Log
            </a>
            <a href="{{ route('reports.index', ['type' => 'payroll']) }}" class="px-4 py-2 rounded-lg transition {{ $reportType === 'payroll' ? 'bg-emerald-600 text-white shadow' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Payroll Payouts
            </a>
            <a href="{{ route('reports.index', ['type' => 'academics']) }}" class="px-4 py-2 rounded-lg transition {{ $reportType === 'academics' ? 'bg-emerald-600 text-white shadow' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <i class="fa-solid fa-graduation-cap me-1"></i> Academic Admissions
            </a>
        </div>
        <button onclick="window.print()" class="px-3.5 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl shadow">
            <i class="fa-solid fa-print me-1"></i> Export PDF / Print
        </button>
    </div>

    <!-- Financial Report View -->
    @if($reportType === 'financial')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold text-slate-400">Total Income</p>
                <h4 class="text-xl font-bold text-emerald-600 mt-1">Rs. {{ number_format($financialSummary['total_income'], 2) }}</h4>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold text-slate-400">Total Expenses</p>
                <h4 class="text-xl font-bold text-red-500 mt-1">Rs. {{ number_format($financialSummary['total_expense'], 2) }}</h4>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold text-slate-400">Fee Collected</p>
                <h4 class="text-xl font-bold text-slate-900 mt-1">Rs. {{ number_format($financialSummary['paid_fees'], 2) }}</h4>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-bold text-slate-400">Fee Outstanding</p>
                <h4 class="text-xl font-bold text-amber-600 mt-1">Rs. {{ number_format($financialSummary['unpaid_fees'], 2) }}</h4>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="font-bold text-sm text-slate-800">Category Wise Breakdown</h4>
            <table class="w-full text-xs text-left text-slate-600 border">
                <thead class="bg-slate-50 font-bold uppercase border-b">
                    <tr>
                        <th class="p-3">Category</th>
                        <th class="p-3">Type</th>
                        <th class="p-3 text-right">Sum Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($financialSummary['categories'] as $cat)
                        <tr>
                            <td class="p-3 font-bold text-slate-900 uppercase">{{ str_replace('_', ' ', $cat->category) }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $cat->entry_type === 'credit' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $cat->entry_type }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-bold text-slate-900">Rs. {{ number_format($cat->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Attendance Report View -->
    @if($reportType === 'attendance')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400">Total Attendance Logs</p>
            <h4 class="text-xl font-bold text-slate-900 mt-1">{{ number_format($attendanceSummary['total_records']) }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400">Present Count</p>
            <h4 class="text-xl font-bold text-emerald-600 mt-1">{{ number_format($attendanceSummary['present_count']) }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400">Late Delays</p>
            <h4 class="text-xl font-bold text-amber-600 mt-1">{{ number_format($attendanceSummary['late_count']) }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400">Absences</p>
            <h4 class="text-xl font-bold text-red-500 mt-1">{{ number_format($attendanceSummary['absent_count']) }}</h4>
        </div>
    </div>
    @endif

    <!-- Payroll Report View -->
    @if($reportType === 'payroll')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400">Total Net Disbursed</p>
            <h4 class="text-xl font-bold text-emerald-700 mt-1">Rs. {{ number_format($payrollSummary['total_disbursed'], 2) }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400">Total Allowances</p>
            <h4 class="text-xl font-bold text-blue-600 mt-1">Rs. {{ number_format($payrollSummary['total_allowances'], 2) }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400">Total Deductions (Tax)</p>
            <h4 class="text-xl font-bold text-red-500 mt-1">Rs. {{ number_format($payrollSummary['total_deductions'], 2) }}</h4>
        </div>
    </div>
    @endif

</div>
@endsection
