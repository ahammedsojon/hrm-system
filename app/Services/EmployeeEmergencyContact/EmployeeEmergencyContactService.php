<?php

namespace App\Services\EmployeeEmergencyContact;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeEmergencyContactService
{
    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return EmployeeEmergencyContact::query()
            ->with('employee')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('relationship', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('priority')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function find(int $id): EmployeeEmergencyContact
    {
        return EmployeeEmergencyContact::query()
            ->with('employee')
            ->findOrFail($id);
    }

    public function create(array $data): EmployeeEmergencyContact
    {
        return EmployeeEmergencyContact::query()
            ->create($data)
            ->load('employee');
    }

    public function update(EmployeeEmergencyContact $contact, array $data): EmployeeEmergencyContact
    {
        $contact->update($data);

        return $contact->fresh(['employee']);
    }

    public function delete(EmployeeEmergencyContact $contact): void
    {
        $contact->delete();
    }
}
