<?php

namespace App\Http\Requests\Designation;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesignationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:designations,name'],
            'level' => ['required', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string'],
        ];
    }
}
