<?php

namespace App\Http\Requests\EmployeeEmergencyContact;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeEmergencyContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'relationship' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'priority' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }
}
