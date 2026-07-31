<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage hr');
    }

    public function rules(): array
    {
        return [
            'department_id' => 'nullable|exists:departments,id',
            'employee_code' => 'required|string|unique:employees,employee_code',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:20',
            'designation' => 'required|string|max:255',
            'type' => 'required|in:faculty,staff,administration',
            'basic_salary' => 'required|numeric|min:0',
            'joining_date' => 'nullable|date',
            'status' => 'required|in:active,on_leave,suspended,resigned',
        ];
    }
}
