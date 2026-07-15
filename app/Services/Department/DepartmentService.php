<?php

namespace App\Services\Department;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class DepartmentService
{
    public function all(?string $search = null): Collection
    {
        return Department::query()
            ->with('departmentHead')
            ->withCount('employees')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Department
    {
        return Department::query()
            ->with('departmentHead')
            ->withCount('employees')
            ->findOrFail($id);
    }

    public function create(array $data): Department
    {
        return Department::query()->create($data)->load('departmentHead');
    }

    public function update(Department $department, array $data): Department
    {
        $department->update($data);

        return $department->fresh(['departmentHead'])->loadCount('employees');
    }

    public function delete(Department $department): void
    {
        if ($department->employees()->exists()) {
            throw ValidationException::withMessages([
                'department' => ['Cannot delete a department that has employees assigned.'],
            ]);
        }

        $department->delete();
    }
}
