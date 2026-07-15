<?php

namespace App\Http\Requests\Employee;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class EmployeeRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  protected function employeeRules(?int $employeeId = null, bool $isCreate = false): array
  {
    $rules = [
      'first_name' => ['required', 'string', 'max:100'],
      'last_name' => ['required', 'string', 'max:100'],
      'email' => [
        'required',
        'email',
        'max:255',
        Rule::unique('employees', 'email')->ignore($employeeId),
        Rule::unique('users', 'email')->ignore(
          $employeeId ? Employee::query()->find($employeeId)?->user_id : null
        ),
      ],
      'phone' => ['nullable', 'string', 'max:30'],
      'address' => ['nullable', 'string'],
      'date_of_birth' => [$isCreate ? 'required' : 'nullable', 'date'],
      'blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
      'gender' => ['nullable', Rule::in(['male', 'female'])],
      'profile_photo' => ['nullable', 'image', 'max:2048'],
      'department_id' => ['nullable', 'exists:departments,id'],
      'designation_id' => ['nullable', 'exists:designations,id'],
      'status' => ['required', Rule::in(Employee::STATUSES)],
      'joining_date' => ['nullable', 'date'],
      'probation_start_date' => ['nullable', 'date'],
      'probation_end_date' => ['nullable', 'date', 'after_or_equal:joining_date'],
      'employment_type_id' => ['nullable', 'exists:employment_types,id'],
      'manager_id' => [
        'nullable',
        'exists:employees,id',
        Rule::notIn(array_filter([$employeeId])),
      ],
      'branch_id' => ['nullable', 'integer'],
      'shift_id' => ['nullable', 'integer'],
      'marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])],
      'national_id' => ['nullable', 'string', 'max:50'],
      'passport_no' => ['nullable', 'string', 'max:50'],
      'tax_number' => ['nullable', 'string', 'max:50'],
      'bank_account' => ['nullable', 'string', 'max:50'],
      'bank_name' => ['nullable', 'string', 'max:100'],
      'religion' => ['nullable', 'string', 'max:50'],
      'nationality' => ['nullable', 'string', 'max:50'],
    ];

    return $rules;
  }
}
