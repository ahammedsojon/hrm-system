<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeService
{
  private const DEFAULT_PASSWORD = '12345678!@';

  public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
  {
    return Employee::query()
      ->with(['department', 'designation', 'manager'])
      ->when($search, function ($query, $search) {
        $query->where(function ($query) use ($search) {
          $query->where('email', 'like', "%{$search}%")
            ->orWhere('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%");
        });
      })
      ->latest()
      ->paginate($perPage);
  }

  public function find(int $id): Employee
  {
    return Employee::query()
      ->with(['user', 'department', 'designation', 'manager', 'emergencyContacts'])
      ->findOrFail($id);
  }

  public function managers(?int $excludeId = null)
  {
    return Employee::query()
      ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
      ->orderBy('first_name')
      ->orderBy('last_name')
      ->get(['id', 'first_name', 'last_name']);
  }

  public function create(array $data, ?UploadedFile $profilePhoto = null): Employee
  {
    return DB::transaction(function () use ($data, $profilePhoto) {
      $user = User::query()->create([
        'email' => $data['email'],
        'password' => self::DEFAULT_PASSWORD,
        'need_password_change' => true,
      ]);

      $employeeRole = Role::query()->where('name', Role::EMPLOYEE)->firstOrFail();
      $user->roles()->attach($employeeRole->id);

      if ($profilePhoto) {
        $data['profile_photo'] = $this->storePhoto($profilePhoto);
      }

      $data['user_id'] = $user->id;

      $employee = Employee::query()->create($data);

      return $employee->load(['user', 'department', 'designation', 'manager']);
    });
  }

  public function update(Employee $employee, array $data, ?UploadedFile $profilePhoto = null): Employee
  {
    return DB::transaction(function () use ($employee, $data, $profilePhoto) {
      if ($profilePhoto) {
        if ($employee->profile_photo) {
          Storage::disk('public')->delete($employee->profile_photo);
        }
        $data['profile_photo'] = $this->storePhoto($profilePhoto);
      }

      $employee->update($data);

      $employee->user?->update([
        'email' => $employee->email,
      ]);

      return $employee->fresh(['user', 'department', 'designation', 'manager', 'emergencyContacts']);
    });
  }

  public function delete(Employee $employee): void
  {
    DB::transaction(function () use ($employee) {
      if ($employee->profile_photo) {
        Storage::disk('public')->delete($employee->profile_photo);
      }

      $user = $employee->user;
      $employee->delete();

      $user?->delete();
    });
  }

  private function storePhoto(UploadedFile $photo): string
  {
    return $photo->store('employees', 'public');
  }
}
