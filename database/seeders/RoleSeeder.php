<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => Role::SUPER_ADMIN,
                'description' => 'System configuration, user management, permissions.',
            ],
            [
                'name' => Role::HR,
                'description' => 'Employee management, attendance, leave, payroll processing.',
            ],
            [
                'name' => Role::MANAGER,
                'description' => 'Team management, leave approval, performance reviews.',
            ],
            [
                'name' => Role::EMPLOYEE,
                'description' => 'Personal profile, attendance, leave requests, payslips.',
            ],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']],
            );
        }
    }
}
