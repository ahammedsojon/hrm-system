<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::query()->where('name', Role::SUPER_ADMIN)->firstOrFail();

        $user = User::query()->updateOrCreate(
            ['email' => 'admin@owner.com'],
            [
                'name' => 'Super Admin',
                'password' => '12345678!@',
                'need_password_change' => false,
            ],
        );

        $user->roles()->sync([$role->id]);
    }
}
