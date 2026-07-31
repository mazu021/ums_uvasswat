<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\FeeChallan;
use App\Models\LedgerEntry;
use App\Models\Payroll;
use App\Models\Student;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->get('type', 'financial');

        $financialSummary = null;
        $attendanceSummary = null;
        $payrollSummary = null;
        $studentsSummary = null;

        if ($reportType === 'financial') {
            $financialSummary = [
                'total_income' => LedgerEntry::where('entry_type', 'credit')->sum('amount'),
                'total_expense' => LedgerEntry::where('entry_type', 'debit')->sum('amount'),
                'paid_fees' => FeeChallan::where('status', 'paid')->sum('paid_amount'),
                'unpaid_fees' => FeeChallan::whereIn('status', ['unpaid', 'overdue'])->sum('total_amount'),
                'categories' => LedgerEntry::selectRaw('category, entry_type, SUM(amount) as total')->groupBy('category', 'entry_type')->get(),
            ];
        } elseif ($reportType === 'attendance') {
            $attendanceSummary = [
                'total_records' => Attendance::count(),
                'present_count' => Attendance::where('status', 'present')->count(),
                'absent_count' => Attendance::where('status', 'absent')->count(),
                'late_count' => Attendance::where('status', 'late')->count(),
                'on_leave_count' => Attendance::where('status', 'on_leave')->count(),
            ];
        } elseif ($reportType === 'payroll') {
            $payrollSummary = [
                'total_disbursed' => Payroll::sum('net_salary'),
                'total_basic' => Payroll::sum('basic_salary'),
                'total_allowances' => Payroll::sum('allowances'),
                'total_deductions' => Payroll::sum('deductions'),
                'total_payslips' => Payroll::count(),
            ];
        } elseif ($reportType === 'academics') {
            $studentsSummary = [
                'active_students' => Student::where('status', 'active')->count(),
                'graduated_students' => Student::where('status', 'graduated')->count(),
                'total_faculty' => Employee::where('type', 'faculty')->count(),
            ];
        }

        return view('reports.index', compact('reportType', 'financialSummary', 'attendanceSummary', 'payrollSummary', 'studentsSummary'));
    }

    public function printReport(Request $request)
    {
        $type = $request->get('type', 'financial');
        return view('reports.print', compact('type'));
    }
}
