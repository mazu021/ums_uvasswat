<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip {{ $payroll->payslip_number }} - UVAS Swat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 flex justify-center text-slate-800">

    <div class="w-full max-w-2xl bg-white p-8 rounded-2xl shadow-xl border border-slate-200 space-y-6">

        <!-- Print Action Top Bar -->
        <div class="no-print flex justify-end space-x-2 border-b pb-4">
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow hover:bg-emerald-700">
                Print Official Payslip
            </button>
        </div>

        <!-- Institutional Header -->
        <div class="text-center border-b pb-6">
            <h1 class="text-xl font-bold text-slate-900 uppercase tracking-wide">The University of Veterinary and Animal Sciences, Swat (UVAS Swat)</h1>
            <p class="text-xs font-semibold text-emerald-700 mt-1">OFFICIAL MONTHLY SALARY PAYSLIP</p>
            <p class="text-[11px] text-slate-500">Kabal Road, Saidu Sharif, Swat, KP, Pakistan</p>
        </div>

        <!-- Payslip Metadata Grid -->
        <div class="grid grid-cols-2 gap-4 text-xs border bg-slate-50 p-4 rounded-xl">
            <div>
                <p><span class="font-bold text-slate-600">Payslip No:</span> <strong class="text-slate-900">{{ $payroll->payslip_number }}</strong></p>
                <p class="mt-1"><span class="font-bold text-slate-600">Employee Name:</span> {{ $payroll->employee->full_name }}</p>
                <p class="mt-1"><span class="font-bold text-slate-600">Employee Code:</span> {{ $payroll->employee->employee_code }}</p>
            </div>
            <div>
                <p><span class="font-bold text-slate-600">Pay Period:</span> {{ date('F', mktime(0, 0, 0, $payroll->month, 10)) }} {{ $payroll->year }}</p>
                <p class="mt-1"><span class="font-bold text-slate-600">Designation:</span> {{ $payroll->employee->designation }}</p>
                <p class="mt-1"><span class="font-bold text-slate-600">Department:</span> {{ $payroll->employee->department->name }}</p>
            </div>
        </div>

        <!-- Financial Breakdown Table -->
        <table class="w-full text-xs text-left border rounded-xl overflow-hidden">
            <thead class="bg-navy-900 text-white font-bold uppercase">
                <tr>
                    <th class="p-3">Description</th>
                    <th class="p-3 text-right">Amount (PKR)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <tr>
                    <td class="p-3 font-semibold">Basic Pay Scale Salary</td>
                    <td class="p-3 text-right font-bold">Rs. {{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                <tr>
                    <td class="p-3 font-semibold text-emerald-700">Allowances (Housing & Medical)</td>
                    <td class="p-3 text-right font-bold text-emerald-700">+ Rs. {{ number_format($payroll->allowances, 2) }}</td>
                </tr>
                <tr>
                    <td class="p-3 font-semibold text-red-600">Deductions (Income Tax & Provident Fund)</td>
                    <td class="p-3 text-right font-bold text-red-600">- Rs. {{ number_format($payroll->deductions, 2) }}</td>
                </tr>
                <tr class="bg-slate-100 font-bold text-sm">
                    <td class="p-3 uppercase">Net Salary Payable</td>
                    <td class="p-3 text-right text-emerald-800">Rs. {{ number_format($payroll->net_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures & Verification Footnote -->
        <div class="pt-12 grid grid-cols-2 gap-8 text-xs text-center border-t">
            <div>
                <div class="border-b border-slate-400 w-3/4 mx-auto mb-1"></div>
                <p class="font-bold text-slate-700">Finance Officer Sign</p>
            </div>
            <div>
                <div class="border-b border-slate-400 w-3/4 mx-auto mb-1"></div>
                <p class="font-bold text-slate-700">Treasurer UVAS Swat</p>
            </div>
        </div>

    </div>

</body>
</html>
