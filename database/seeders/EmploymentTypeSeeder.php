<?php

namespace Database\Seeders;

use App\Models\EmploymentType;
use Illuminate\Database\Seeder;

class EmploymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Full Time',
            'Part Time',
            'Contract',
            'Intern',
        ];

        foreach ($types as $type) {
            EmploymentType::query()->updateOrCreate(['name' => $type]);
        }
    }
}
