<?php

namespace App\Services\Designation;

use App\Models\Designation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class DesignationService
{
    public function all(?string $search = null): Collection
    {
        return Designation::query()
            ->withCount('employees')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('level')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Designation
    {
        return Designation::query()
            ->withCount('employees')
            ->findOrFail($id);
    }

    public function create(array $data): Designation
    {
        return Designation::query()->create($data);
    }

    public function update(Designation $designation, array $data): Designation
    {
        $designation->update($data);

        return $designation->fresh()->loadCount('employees');
    }

    public function delete(Designation $designation): void
    {
        if ($designation->employees()->exists()) {
            throw ValidationException::withMessages([
                'designation' => ['Cannot delete a designation that has employees assigned.'],
            ]);
        }

        $designation->delete();
    }
}
