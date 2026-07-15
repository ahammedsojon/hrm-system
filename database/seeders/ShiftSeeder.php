<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            ['name' => 'Day Shift', 'start_time' => '09:00', 'end_time' => '17:00', 'working_hours' => 8],
            ['name' => 'Morning Shift', 'start_time' => '06:00', 'end_time' => '14:00', 'working_hours' => 8],
            ['name' => 'Evening Shift', 'start_time' => '14:00', 'end_time' => '22:00', 'working_hours' => 8],
        ];

        foreach ($shifts as $shift) {
            Shift::query()->updateOrCreate(
                ['name' => $shift['name']],
                $shift,
            );
        }
    }
}
