@extends('layouts.app')

@section('title', 'General Ledger')
@section('header_title', 'General Ledger & Expense Logger')

@section('content')
<div class="space-y-6" x-data="{ entryModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">University Accounts Ledger</h3>
            <p class="text-xs text-slate-500">Record institutional revenue deposits, operational expenditures, and view trial balance.</p>
        </div>
        <button @click="entryModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow flex items-center space-x-2">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Add Ledger Transaction</span>
        </button>
    </div>

    <!-- Balance Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase">Total Revenue (Credit)</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">Rs. {{ number_format($totalCredit, 2) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-arrow-down-left"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase">Total Expenditure (Debit)</p>
                <h3 class="text-2xl font-bold text-red-500 mt-1">Rs. {{ number_format($totalDebit, 2) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-arrow-up-right"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase">Net Treasury Reserve</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">Rs. {{ number_format($balance, 2) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase border-b">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Title / Description</th>
                        <th class="px-6 py-3">Ref No.</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($entries as $en)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $en->transaction_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $en->entry_type === 'credit' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $en->entry_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700 uppercase">{{ str_replace('_', ' ', $en->category) }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $en->title }}
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $en->description }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $en->reference_number ?? '-' }}</td>
                            <td class="px-6 py-4 font-bold text-sm {{ $en->entry_type === 'credit' ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ $en->entry_type === 'credit' ? '+' : '-' }} Rs. {{ number_format($en->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $en->creator->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-6 text-center text-slate-400">No ledger entries recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t bg-slate-50">
            {{ $entries->links() }}
        </div>
    </div>

    <!-- Record Transaction Modal -->
    <div x-show="entryModal" x-transition class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="entryModal = false">
            <div class="px-6 py-4 bg-navy-900 text-white flex items-center justify-between">
                <h4 class="font-bold text-sm">Add Ledger Transaction</h4>
                <button @click="entryModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('finance.accounts.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Transaction Type</label>
                        <select name="entry_type" class="w-full px-3 py-2 border rounded-lg">
                            <option value="credit">Income (Credit)</option>
                            <option value="debit">Expense (Debit)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2 border rounded-lg">
                            <option value="fee_collection">Fee Collection</option>
                            <option value="salary_payment">Salary Payment</option>
                            <option value="lab_equipment">Lab Equipment</option>
                            <option value="maintenance">Maintenance & Utilities</option>
                            <option value="grant">Research Grant</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Transaction Title</label>
                    <input type="text" name="title" required placeholder="e.g. Chemicals purchase for DVM lab" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Amount (PKR)</label>
                        <input type="number" step="0.01" name="amount" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Date</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Reference No. / PO Number</label>
                    <input type="text" name="reference_number" placeholder="e.g. PO-2026-099" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Notes / Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-2 border-t">
                    <button type="button" @click="entryModal = false" class="px-3 py-1.5 bg-slate-100 text-slate-700 font-bold rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white font-bold rounded-lg shadow">Record Entry</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
