<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Challan - {{ $feeChallan->challan_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; background: white; }
            .challan-container { width: 100% !important; max-width: 100% !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 font-sans text-slate-900">

    <!-- Action Header -->
    <div class="no-print max-w-6xl mx-auto mb-4 flex items-center justify-between bg-slate-900 text-white p-4 rounded-xl shadow-md">
        <div>
            <h2 class="font-bold text-sm">Official Bank Fee Deposit Voucher</h2>
            <p class="text-xs text-slate-300">Challan No: {{ $feeChallan->challan_number }}</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-white font-extrabold text-xs rounded-lg transition flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Print Bank Voucher (PDF)</span>
        </button>
    </div>

    <!-- 3-Copy Bank Challan Grid -->
    <div class="challan-container max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4">
        
        @php
            $copies = ['BANK COPY', 'UNIVERSITY COPY', 'STUDENT COPY'];
        @endphp

        @foreach($copies as $copyName)
            <div class="bg-white p-5 border-2 border-slate-800 rounded-xl space-y-3 text-xs flex flex-col justify-between">
                <div>
                    <!-- Bank & Institution Header -->
                    <div class="text-center border-b pb-2 space-y-1">
                        <h1 class="font-extrabold text-xs uppercase tracking-tight">The University of Veterinary and Animal Sciences, Swat</h1>
                        <p class="text-[10px] font-bold text-emerald-700 uppercase">HBL Account No: 1234-56789012-03</p>
                        <p class="text-[10px] text-slate-500 font-semibold">Title: UVAS Swat Fee Collection Account</p>
                        <div class="mt-1 inline-block px-3 py-0.5 bg-slate-900 text-white text-[10px] font-black rounded-full uppercase tracking-wider">
                            {{ $copyName }}
                        </div>
                    </div>

                    <!-- Challan & Student Metadata -->
                    <div class="my-3 space-y-1.5 border-b pb-3 text-[11px]">
                        <div class="flex justify-between font-bold">
                            <span class="text-slate-500">Challan No:</span>
                            <span class="font-mono text-slate-900">{{ $feeChallan->challan_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Issue Date:</span>
                            <span>{{ $feeChallan->issue_date ? $feeChallan->issue_date->format('d-M-Y') : date('d-M-Y') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-rose-600">
                            <span>Due Date:</span>
                            <span>{{ $feeChallan->due_date ? $feeChallan->due_date->format('d-M-Y') : 'N/A' }}</span>
                        </div>
                        <div class="pt-1 border-t border-slate-100 flex justify-between">
                            <span class="text-slate-500">Student Name:</span>
                            <span class="font-bold text-slate-900">{{ $feeChallan->student->full_name ?? 'Student' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Reg / Roll:</span>
                            <span class="font-mono font-bold">{{ $feeChallan->student->registration_number ?? ($feeChallan->student->roll_number ?? 'N/A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Program:</span>
                            <span class="font-semibold text-slate-800">{{ $feeChallan->student->program->name ?? ($feeChallan->student->department->name ?? 'N/A') }}</span>
                        </div>
                    </div>

                    <!-- Particulars & Amounts -->
                    <div class="space-y-1 text-[11px]">
                        <h4 class="font-bold text-[10px] uppercase text-slate-500 mb-1">Fee Particulars</h4>
                        <div class="flex justify-between py-0.5 border-b border-slate-100">
                            <span>Tuition Fee:</span>
                            <span class="font-mono font-bold">Rs. {{ number_format($feeChallan->feeStructure->tuition_fee ?? 45000, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-0.5 border-b border-slate-100">
                            <span>Admission / Reg Fee:</span>
                            <span class="font-mono">Rs. {{ number_format($feeChallan->feeStructure->admission_fee ?? 5000, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-0.5 border-b border-slate-100">
                            <span>Examination Fee:</span>
                            <span class="font-mono">Rs. {{ number_format($feeChallan->feeStructure->examination_fee ?? 3000, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-0.5 border-b border-slate-100">
                            <span>Library & Lab Charges:</span>
                            <span class="font-mono">Rs. {{ number_format(($feeChallan->feeStructure->library_fee ?? 1000) + ($feeChallan->feeStructure->other_charges ?? 1000), 2) }}</span>
                        </div>

                        @if(($feeChallan->late_fine_amount ?? 0) > 0)
                            <div class="flex justify-between py-0.5 border-b border-slate-100 text-rose-600 font-bold">
                                <span>Late Fee Fine:</span>
                                <span class="font-mono">Rs. {{ number_format($feeChallan->late_fine_amount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between py-2 border-t-2 border-slate-900 font-black text-xs text-slate-900 bg-slate-50 px-1 mt-2">
                            <span>TOTAL PAYABLE:</span>
                            <span class="font-mono">Rs. {{ number_format($feeChallan->total_amount + ($feeChallan->late_fine_amount ?? 0), 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Bank Deposit Officer & Verification Stamp Box -->
                <div class="pt-4 border-t border-slate-200 mt-4 space-y-4">
                    <p class="text-[9px] text-slate-400 italic">
                        * Deposited fees are non-refundable and non-transferable.
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-[10px] font-bold text-slate-700">
                        <div class="border-t border-slate-400 pt-1 text-center">
                            Depositor Signature
                        </div>
                        <div class="border-t border-slate-400 pt-1 text-center">
                            Bank Officer Stamp
                        </div>
                    </div>
                </div>

            </div>
        @endforeach

    </div>

</body>
</html>
