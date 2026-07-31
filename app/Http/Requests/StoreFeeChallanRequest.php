<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeChallanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage finance');
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'fee_structure_id' => 'nullable|exists:fee_structures,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'total_amount' => 'required|numeric|min:0',
        ];
    }
}
