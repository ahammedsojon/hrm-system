<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            ['name' => 'Software Engineer', 'level' => 2, 'description' => 'Develops and maintains software applications.'],
            ['name' => 'HR Manager', 'level' => 4, 'description' => 'Oversees human resources functions and policies.'],
            ['name' => 'Team Lead', 'level' => 3, 'description' => 'Leads a team and coordinates project delivery.'],
            ['name' => 'Accountant', 'level' => 2, 'description' => 'Manages financial records and reporting.'],
        ];

        foreach ($designations as $designation) {
            Designation::query()->updateOrCreate(
                ['name' => $designation['name']],
                [
                    'level' => $designation['level'],
                    'description' => $designation['description'],
                ],
            );
        }
    }
}
