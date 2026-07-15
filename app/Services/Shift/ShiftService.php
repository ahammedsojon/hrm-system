<?php

namespace App\Services\Shift;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ShiftService
{
    public function all(?string $search = null): Collection
    {
        return Shift::query()
            ->withCount('employees')
            ->when($search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Shift
    {
        return Shift::query()
            ->withCount('employees')
            ->findOrFail($id);
    }

    public function create(array $data): Shift
    {
        return Shift::query()->create($data);
    }

    public function update(Shift $shift, array $data): Shift
    {
        $shift->update($data);

        return $shift->fresh()->loadCount('employees');
    }

    public function delete(Shift $shift): void
    {
        if ($shift->employees()->exists()) {
            throw ValidationException::withMessages([
                'shift' => ['Cannot delete a shift that has employees assigned.'],
            ]);
        }

        $shift->delete();
    }
}
