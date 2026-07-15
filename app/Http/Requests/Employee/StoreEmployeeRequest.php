<?php

namespace App\Http\Requests\Employee;

class StoreEmployeeRequest extends EmployeeRequest
{
  public function rules(): array
  {
    return $this->employeeRules(isCreate: true);
  }
}
