<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->get('month', date('n'));
        $year = (int) $request->get('year', date('Y'));

        $perPage = $request->get('per_page', 100);
        $payrolls = Payroll::with(['employee.department'])
            ->where('month', $month)
            ->where('year', $year)
            ->paginate($perPage);

        $employees = Employee::where('status', 'active')->get();

        return view('hr.payroll', compact('payrolls', 'employees', 'month', 'year'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $basic = $employee->basic_salary;
        $allowances = $validated['allowances'] ?? 0;
        $deductions = $validated['deductions'] ?? 0;
        $netSalary = ($basic + $allowances) - $deductions;

        $payslipNo = 'PAY-' . $validated['year'] . str_pad($validated['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($employee->id, 3, '0', STR_PAD_LEFT);

        $payroll = Payroll::updateOrCreate(
            ['employee_id' => $employee->id, 'month' => $validated['month'], 'year' => $validated['year']],
            [
                'payslip_number' => $payslipNo,
                'basic_salary' => $basic,
                'allowances' => $allowances,
                'deductions' => $deductions,
                'net_salary' => $netSalary,
                'payment_status' => 'paid',
                'payment_date' => Carbon::today(),
            ]
        );

        AuditService::log('Generated Monthly Payslip', 'Payroll', $payroll->id, ['payslip_number' => $payslipNo]);

        return back()->with('success', "Payslip {$payslipNo} generated and processed successfully.");
    }

    public function payslip(Payroll $payroll)
    {
        $payroll->load(['employee.department']);
        return view('hr.payslip', compact('payroll'));
    }
}
