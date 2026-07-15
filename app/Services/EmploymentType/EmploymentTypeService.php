<?php

namespace App\Services\EmploymentType;

use App\Models\EmploymentType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class EmploymentTypeService
{
    public function all(?string $search = null): Collection
    {
        return EmploymentType::query()
            ->withCount('employees')
            ->when($search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): EmploymentType
    {
        return EmploymentType::query()
            ->withCount('employees')
            ->findOrFail($id);
    }

    public function create(array $data): EmploymentType
    {
        return EmploymentType::query()->create($data);
    }

    public function update(EmploymentType $employmentType, array $data): EmploymentType
    {
        $employmentType->update($data);

        return $employmentType->fresh()->loadCount('employees');
    }

    public function delete(EmploymentType $employmentType): void
    {
        if ($employmentType->employees()->exists()) {
            throw ValidationException::withMessages([
                'employment_type' => ['Cannot delete an employment type that has employees assigned.'],
            ]);
        }

        $employmentType->delete();
    }
}
