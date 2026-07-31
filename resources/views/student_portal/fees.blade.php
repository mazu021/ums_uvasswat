@extends('layouts.app')

@section('title', 'My Fee Challans & Payment Status')
@section('header_title', 'Student Fee Portal & Slip Verification')

@section('content')
<div class="space-y-6" x-data="{ showUploadModal: false, activeChallan: null, showSlipModal: false }">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-navy-900 to-indigo-950 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="px-2.5 py-0.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[11px] font-bold rounded-full">
                    <i class="fa-solid fa-money-check-dollar me-1"></i> Directorate of Finance & Accounts
                </span>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">Semester Fee Challans</h1>
            <p class="text-slate-300 text-xs mt-1">
                View your semester fee breakdown, print official bank vouchers, and submit deposit receipt slips for verification.
            </p>
        </div>
    </div>

    <!-- Clean Semester Fee Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-base text-slate-900">Fee Challans History</h3>
                <p class="text-xs text-slate-500">Official semester fee records issued for your degree program.</p>
            </div>
            <span class="px-3 py-1 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">
                Total Issued: {{ $challans->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[850px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="p-4 w-12 text-center">#</th>
                        <th class="p-4">Semester & Challan No</th>
                        <th class="p-4">Fee Breakdown</th>
                        <th class="p-4 text-right">Total Payable</th>
                        <th class="p-4 text-center">Payment Status</th>
                        <th class="p-4 text-center">Actions & Slip</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($challans as $index => $challan)
                        @php
                            $isPaid = $challan->status === 'paid';
                            $isPending = $challan->status === 'pending_verification';
                            $isRejected = $challan->status === 'rejected_reupload';
                            $isOverdue = $challan->status === 'overdue' || ($challan->due_date && \Carbon\Carbon::today()->greaterThan($challan->due_date) && !$isPaid);
                            $totalPayable = $challan->total_amount + ($challan->late_fine_amount ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $isRejected ? 'bg-rose-50/20' : '' }}">
                            <td class="p-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                            
                            <!-- Semester & Challan -->
                            <td class="p-4 space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-900 font-black text-[11px] rounded-md border border-indigo-200">
                                        Semester {{ $challan->semester ?? 1 }}
                                    </span>
                                    <span class="font-mono font-bold text-slate-900 text-xs">
                                        {{ $challan->challan_number }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    <span>Due Date: <strong class="{{ $isOverdue ? 'text-rose-600' : 'text-slate-700' }}">{{ $challan->due_date ? $challan->due_date->format('M d, Y') : 'N/A' }}</strong></span>
                                </div>
                            </td>

                            <!-- Fee Breakdown Summary -->
                            <td class="p-4 space-y-0.5 text-[11px]">
                                <div class="text-slate-700">Tuition Fee: <strong class="font-mono">Rs. {{ number_format($challan->feeStructure->tuition_fee ?? $challan->total_amount, 0) }}</strong></div>
                                @if(($challan->feeStructure->admission_fee ?? 0) > 0)
                                    <div class="text-slate-500">Admission/Reg: <span class="font-mono">Rs. {{ number_format($challan->feeStructure->admission_fee, 0) }}</span></div>
                                @endif
                                @if(($challan->late_fine_amount ?? 0) > 0)
                                    <div class="text-rose-600 font-bold">Late Fine: <span class="font-mono">Rs. {{ number_format($challan->late_fine_amount, 0) }}</span></div>
                                @endif
                            </td>

                            <!-- Total Payable -->
                            <td class="p-4 text-right">
                                <strong class="font-mono font-black text-sm text-slate-900 block">Rs. {{ number_format($totalPayable, 2) }}</strong>
                                <span class="text-[10px] text-slate-400 font-semibold uppercase">HBL Bank Deposit</span>
                            </td>

                            <!-- Status Badge -->
                            <td class="p-4 text-center">
                                @if($isPaid)
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-extrabold text-[11px] rounded-lg inline-flex items-center gap-1 shadow-2xs">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i> PAID & VERIFIED
                                    </span>
                                @elseif($isPending)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 font-extrabold text-[11px] rounded-lg inline-flex items-center gap-1 shadow-2xs">
                                        <i class="fa-solid fa-spinner fa-spin text-blue-600"></i> PENDING VERIFICATION
                                    </span>
                                @elseif($isRejected)
                                    <div class="space-y-1">
                                        <span class="px-3 py-1 bg-rose-100 text-rose-800 font-extrabold text-[11px] rounded-lg inline-flex items-center gap-1 shadow-2xs">
                                            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> REJECTED (RE-UPLOAD)
                                        </span>
                                        @if($challan->rejection_reason)
                                            <p class="text-[10px] text-rose-700 font-semibold italic max-w-xs mx-auto" title="{{ $challan->rejection_reason }}">
                                                "{{ Str::limit($challan->rejection_reason, 40) }}"
                                            </p>
                                        @endif
                                    </div>
                                @elseif($isOverdue)
                                    <span class="px-3 py-1 bg-amber-100 text-amber-800 font-extrabold text-[11px] rounded-lg inline-flex items-center gap-1 shadow-2xs">
                                        <i class="fa-solid fa-clock text-amber-600"></i> OVERDUE (FINE APPLIED)
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-slate-100 text-slate-700 font-extrabold text-[11px] rounded-lg inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-info text-slate-400"></i> UNPAID
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Download / Print Bank Voucher -->
                                    <a href="{{ route('finance.fees.challans.print', $challan->id) }}" 
                                       target="_blank" 
                                       class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center space-x-1"
                                       title="Download 3-Copy Bank Deposit Voucher">
                                        <i class="fa-solid fa-print me-1"></i>
                                        <span>Print Voucher</span>
                                    </a>

                                    <!-- Upload or View Slip -->
                                    @if(!$isPaid)
                                        <button @click="activeChallan = {{ json_encode($challan) }}; showUploadModal = true" 
                                                class="px-3 py-1.5 {{ $isRejected ? 'bg-rose-600 hover:bg-rose-500' : 'bg-emerald-600 hover:bg-emerald-500' }} text-white font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center space-x-1">
                                            <i class="fa-solid {{ $isRejected ? 'fa-rotate' : 'fa-cloud-arrow-up' }} me-1"></i>
                                            <span>{{ $isRejected ? 'Re-upload Slip' : ($isPending ? 'Update Slip' : 'Upload Slip') }}</span>
                                        </button>
                                    @else
                                        @if($challan->payment_proof)
                                            <button @click="activeChallan = {{ json_encode($challan) }}; showSlipModal = true" 
                                                    class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 transition inline-flex items-center space-x-1">
                                                <i class="fa-solid fa-eye me-1"></i>
                                                <span>View Slip</span>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400 space-y-2">
                                <i class="fa-solid fa-file-invoice-dollar text-4xl text-slate-300"></i>
                                <p class="font-bold text-slate-700 text-sm">No fee challans issued yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: Upload / Re-upload Receipt Slip Modal -->
    <div x-show="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="showUploadModal = false" class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="font-bold text-base text-slate-900">Upload Paid Bank Receipt Slip</h3>
                    <p class="text-xs text-slate-500" x-text="activeChallan ? 'Challan # ' + activeChallan.challan_number + ' (Semester ' + (activeChallan.semester || 1) + ')' : ''"></p>
                </div>
                <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <template x-if="activeChallan && activeChallan.rejection_reason">
                <div class="p-3 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl text-rose-900 text-xs space-y-1">
                    <strong class="font-extrabold block">Rejection Reason Note:</strong>
                    <p x-text="activeChallan.rejection_reason" class="font-medium text-rose-800"></p>
                </div>
            </template>

            <form action="{{ route('student.fees.upload-proof', ['feeChallan' => 0]) }}" :action="'/student/fees/challans/' + (activeChallan ? activeChallan.id : 0) + '/upload-proof'" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Transaction Ref # / Deposit Slip No *</label>
                    <input type="text" 
                           name="transaction_reference" 
                           required 
                           :value="activeChallan ? activeChallan.transaction_reference : ''"
                           placeholder="e.g. HBL-98765432" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Upload Receipt Image / PDF *</label>
                    <input type="file" 
                           name="payment_proof" 
                           required 
                           accept="image/*,.pdf"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Payment Notes (Optional)</label>
                    <textarea name="payment_notes" rows="2" placeholder="Any additional notes..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Submit Slip For Finance Verification</span>
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL 2: View Submitted Slip Modal -->
    <div x-show="showSlipModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="showSlipModal = false" class="w-full max-w-lg bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="font-bold text-base text-slate-900">Submitted Payment Deposit Slip</h3>
                    <p class="text-xs text-slate-500" x-text="activeChallan ? 'Challan # ' + activeChallan.challan_number : ''"></p>
                </div>
                <button @click="showSlipModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <template x-if="activeChallan && activeChallan.payment_proof">
                <div class="space-y-3">
                    <div class="p-2 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-center">
                        <a :href="'/storage/' + activeChallan.payment_proof" target="_blank">
                            <img :src="'/storage/' + activeChallan.payment_proof" alt="Submitted Receipt" class="max-h-72 object-contain rounded-lg shadow-xs">
                        </a>
                    </div>
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">Transaction Ref:</span>
                            <strong class="font-mono text-emerald-900" x-text="activeChallan.transaction_reference"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">Verification Status:</span>
                            <strong class="text-emerald-800 font-bold uppercase" x-text="activeChallan.status"></strong>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
