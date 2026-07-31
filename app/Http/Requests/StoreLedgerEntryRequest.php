<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLedgerEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage accounts');
    }

    public function rules(): array
    {
        return [
            'entry_type' => 'required|in:credit,debit',
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ];
    }
}
