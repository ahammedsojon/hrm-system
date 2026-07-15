<?php

namespace App\Http\Requests\Designation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDesignationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $designationId = $this->route('designation')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('designations', 'name')->ignore($designationId),
            ],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }
}
