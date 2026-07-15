<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    private const EMAIL = 'admin@owner.com';

    private const PASSWORD = '12345678!@';

    public function run(): void
    {
        $role = Role::query()->where('name', Role::SUPER_ADMIN)->firstOrFail();

        $existingAdmin = User::query()
            ->whereHas('roles', fn ($query) => $query->where('roles.name', Role::SUPER_ADMIN))
            ->first();

        if ($existingAdmin) {
            return;
        }

        $user = User::query()->create([
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
            'need_password_change' => false,
        ]);

        $user->roles()->attach($role->id);

        Employee::query()->create([
            'user_id' => $user->id,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => self::EMAIL,
            'phone' => '+8801700000001',
            'gender' => 'male',
            'date_of_birth' => '1990-01-15',
            'status' => 'active',
            'joining_date' => '2020-01-01',
            'marital_status' => 'married',
            'nationality' => 'Bangladeshi',
        ]);
    }
}
