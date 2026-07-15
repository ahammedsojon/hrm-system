<?php

namespace App\Http\Requests\EmploymentType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmploymentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employmentTypeId = $this->route('employment_type')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('employment_types', 'name')->ignore($employmentTypeId),
            ],
        ];
    }
}
