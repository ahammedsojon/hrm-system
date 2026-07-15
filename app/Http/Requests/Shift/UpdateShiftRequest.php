<?php

namespace App\Http\Requests\Shift;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $shiftId = $this->route('shift')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('shifts', 'name')->ignore($shiftId),
            ],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'working_hours' => ['required', 'numeric', 'min:0.5', 'max:24'],
        ];
    }
}
