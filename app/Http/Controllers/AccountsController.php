<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLedgerEntryRequest;
use App\Models\LedgerEntry;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');
        $category = $request->get('category');

        $perPage = $request->get('per_page', 100);
        $entries = LedgerEntry::with('creator')
            ->when($type, function ($query, $type) {
                return $query->where('entry_type', $type);
            })
            ->when($category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->latest('transaction_date')
            ->paginate($perPage);

        $totalCredit = LedgerEntry::where('entry_type', 'credit')->sum('amount');
        $totalDebit = LedgerEntry::where('entry_type', 'debit')->sum('amount');
        $balance = $totalCredit - $totalDebit;

        return view('finance.accounts', compact('entries', 'totalCredit', 'totalDebit', 'balance', 'type', 'category'));
    }

    public function store(StoreLedgerEntryRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::id();

        $entry = LedgerEntry::create($validated);
        AuditService::log('Recorded Ledger Entry', 'LedgerEntry', $entry->id, ['amount' => $entry->amount, 'type' => $entry->entry_type]);

        return back()->with('success', 'General ledger entry recorded successfully.');
    }
}
