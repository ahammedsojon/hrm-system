<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'name' => ['required', 'string', 'max:150'],
            'file' => ['nullable', 'file', 'max:10240'],
            'remove_file' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->boolean('remove_file') && ! $this->hasFile('file')) {
                $validator->errors()->add('file', 'Please upload a replacement file.');
            }
        });
    }
}
