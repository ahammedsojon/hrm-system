<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Engineering', 'description' => 'Software development and technical operations.'],
            ['name' => 'Human Resources', 'description' => 'People operations, recruitment, and employee relations.'],
            ['name' => 'Finance', 'description' => 'Accounting, payroll, and financial planning.'],
            ['name' => 'Operations', 'description' => 'Business operations and process management.'],
        ];

        foreach ($departments as $department) {
            Department::query()->updateOrCreate(
                ['name' => $department['name']],
                ['description' => $department['description']],
            );
        }
    }
}
