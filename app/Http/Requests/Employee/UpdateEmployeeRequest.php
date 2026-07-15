<?php

namespace App\Http\Requests\Employee;

class UpdateEmployeeRequest extends EmployeeRequest
{
  public function rules(): array
  {
    $employeeId = (int) $this->route('employee')?->id;

    return $this->employeeRules($employeeId);
  }
}
