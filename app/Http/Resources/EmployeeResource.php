<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->user_id,
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'full_name' => $this->full_name,
      'email' => $this->email,
      'phone' => $this->phone,
      'address' => $this->address,
      'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
      'blood_group' => $this->blood_group,
      'gender' => $this->gender,
      'profile_photo' => $this->profile_photo ? asset('storage/'.$this->profile_photo) : null,
      'department_id' => $this->department_id,
      'designation_id' => $this->designation_id,
      'status' => $this->status,
      'joining_date' => $this->joining_date?->format('Y-m-d'),
      'probation_start_date' => $this->probation_start_date?->format('Y-m-d'),
      'probation_end_date' => $this->probation_end_date?->format('Y-m-d'),
      'employment_type_id' => $this->employment_type_id,
      'manager_id' => $this->manager_id,
      'branch_id' => $this->branch_id,
      'shift_id' => $this->shift_id,
      'marital_status' => $this->marital_status,
      'national_id' => $this->national_id,
      'passport_no' => $this->passport_no,
      'tax_number' => $this->tax_number,
      'bank_account' => $this->bank_account,
      'bank_name' => $this->bank_name,
      'religion' => $this->religion,
      'nationality' => $this->nationality,
      'emergency_contacts' => EmployeeEmergencyContactResource::collection($this->whenLoaded('emergencyContacts')),
      'department' => new DepartmentResource($this->whenLoaded('department')),
      'designation' => new DesignationResource($this->whenLoaded('designation')),
      'employment_type' => new EmploymentTypeResource($this->whenLoaded('employmentType')),
      'manager' => new EmployeeSummaryResource($this->whenLoaded('manager')),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
