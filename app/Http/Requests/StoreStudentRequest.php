<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage academics');
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'registration_number' => 'required|string|unique:students,registration_number',
            'roll_number' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'dob' => 'nullable|date',
            'address' => 'nullable|string',
            'admission_date' => 'nullable|date',
            'current_semester' => 'required|integer|min:1|max:10',
            'status' => 'required|in:active,graduated,suspended',
        ];
    }
}
