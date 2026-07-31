<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'course_code' => 'required|string|unique:courses,course_code',
            'title' => 'required|string|max:255',
            'credit_hours' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ];
    }
}
