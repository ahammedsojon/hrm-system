<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Services\Media\MediaService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    private const DEFAULT_PASSWORD = '12345678!@';

    public function __construct(
        private readonly MediaService $mediaService,
    ) {}

    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Employee::query()
            ->with(['department', 'designation', 'manager', 'profilePhoto', 'shift'])
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
            ->with(['user', 'department', 'designation', 'manager', 'emergencyContacts', 'profilePhoto', 'employmentType', 'shift', 'documents.media'])
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
                $media = $this->mediaService->store($profilePhoto, 'employees', 'profile_photo');
                $data['profile_photo_id'] = $media->id;
            }

            $data['user_id'] = $user->id;

            $employee = Employee::query()->create($data);

            return $employee->load(['user', 'department', 'designation', 'manager', 'profilePhoto']);
        });
    }

    public function update(Employee $employee, array $data, ?UploadedFile $profilePhoto = null, bool $removeProfilePhoto = false): Employee
    {
        return DB::transaction(function () use ($employee, $data, $profilePhoto, $removeProfilePhoto) {
            $oldMedia = $employee->profilePhoto;

            if ($removeProfilePhoto) {
                $data['profile_photo_id'] = null;
            }

            if ($profilePhoto) {
                $media = $this->mediaService->store($profilePhoto, 'employees', 'profile_photo');
                $data['profile_photo_id'] = $media->id;
            }

            $employee->update($data);

            if (($removeProfilePhoto || $profilePhoto) && $oldMedia) {
                $this->mediaService->delete($oldMedia);
            }

            $employee->user?->update([
                'email' => $employee->email,
            ]);

            return $employee->fresh(['user', 'department', 'designation', 'manager', 'emergencyContacts', 'profilePhoto']);
        });
    }

    public function delete(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            $media = $employee->profilePhoto;
            $user = $employee->user;

            $employee->update(['profile_photo_id' => null]);
            $employee->delete();

            $this->mediaService->delete($media);
            $user?->delete();
        });
    }
}
