<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = '12345678!@';

    public function run(): void
    {
        $employeeRole = Role::query()->where('name', Role::EMPLOYEE)->firstOrFail();
        $departments = Department::query()->pluck('id', 'name');
        $designations = Designation::query()->pluck('id', 'name');
        $managerId = Employee::query()
            ->where('email', 'admin@owner.com')
            ->value('id');

        $employees = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@company.com',
                'phone' => '+8801700000002',
                'gender' => 'male',
                'date_of_birth' => '1992-05-20',
                'department_id' => $departments['Engineering'] ?? null,
                'designation_id' => $designations['Software Engineer'] ?? null,
                'status' => 'active',
                'joining_date' => '2021-03-10',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@company.com',
                'phone' => '+8801700000003',
                'gender' => 'female',
                'date_of_birth' => '1993-08-12',
                'department_id' => $departments['Human Resources'] ?? null,
                'designation_id' => $designations['HR Manager'] ?? null,
                'status' => 'active',
                'joining_date' => '2021-06-01',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'michael.brown@company.com',
                'phone' => '+8801700000004',
                'gender' => 'male',
                'date_of_birth' => '1991-11-03',
                'department_id' => $departments['Engineering'] ?? null,
                'designation_id' => $designations['Team Lead'] ?? null,
                'status' => 'active',
                'joining_date' => '2019-09-15',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@company.com',
                'phone' => '+8801700000005',
                'gender' => 'female',
                'date_of_birth' => '1994-02-28',
                'department_id' => $departments['Finance'] ?? null,
                'designation_id' => $designations['Accountant'] ?? null,
                'status' => 'active',
                'joining_date' => '2022-01-20',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'david.wilson@company.com',
                'phone' => '+8801700000006',
                'gender' => 'male',
                'date_of_birth' => '1995-07-07',
                'department_id' => $departments['Engineering'] ?? null,
                'designation_id' => $designations['Software Engineer'] ?? null,
                'status' => 'probation',
                'joining_date' => '2025-01-10',
                'probation_start_date' => '2025-01-10',
                'probation_end_date' => '2025-07-10',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Taylor',
                'email' => 'sarah.taylor@company.com',
                'phone' => '+8801700000007',
                'gender' => 'female',
                'date_of_birth' => '1990-12-18',
                'department_id' => $departments['Operations'] ?? null,
                'designation_id' => $designations['Team Lead'] ?? null,
                'status' => 'active',
                'joining_date' => '2018-04-05',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Anderson',
                'email' => 'robert.anderson@company.com',
                'phone' => '+8801700000008',
                'gender' => 'male',
                'date_of_birth' => '1988-09-25',
                'department_id' => $departments['Finance'] ?? null,
                'designation_id' => $designations['Accountant'] ?? null,
                'status' => 'active',
                'joining_date' => '2017-11-01',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Martinez',
                'email' => 'lisa.martinez@company.com',
                'phone' => '+8801700000009',
                'gender' => 'female',
                'date_of_birth' => '1996-03-14',
                'department_id' => $departments['Human Resources'] ?? null,
                'designation_id' => $designations['Software Engineer'] ?? null,
                'status' => 'probation',
                'joining_date' => '2025-02-01',
                'probation_start_date' => '2025-02-01',
                'probation_end_date' => '2025-08-01',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Thomas',
                'email' => 'james.thomas@company.com',
                'phone' => '+8801700000010',
                'gender' => 'male',
                'date_of_birth' => '1993-10-30',
                'department_id' => $departments['Engineering'] ?? null,
                'designation_id' => $designations['Software Engineer'] ?? null,
                'status' => 'active',
                'joining_date' => '2023-07-15',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
            ],
            [
                'first_name' => 'Olivia',
                'last_name' => 'Harris',
                'email' => 'olivia.harris@company.com',
                'phone' => '+8801700000011',
                'gender' => 'female',
                'date_of_birth' => '1997-04-22',
                'department_id' => $departments['Operations'] ?? null,
                'designation_id' => $designations['Team Lead'] ?? null,
                'status' => 'active',
                'joining_date' => '2024-05-01',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
            ],
        ];

        foreach ($employees as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'password' => self::DEFAULT_PASSWORD,
                    'need_password_change' => true,
                ],
            );

            $user->roles()->syncWithoutDetaching([$employeeRole->id]);

            Employee::query()->updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'user_id' => $user->id,
                    'manager_id' => $managerId,
                ]),
            );
        }
    }
}
