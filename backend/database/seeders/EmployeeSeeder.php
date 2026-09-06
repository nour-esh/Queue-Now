<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Service;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $consultation = Service::where('name', 'General Consultation')->first();
        $dental = Service::where('name', 'Dental')->first();

        Employee::create([
            'name' => 'Dr. Amina',
            'service_id' => $consultation->id,
            'role' => 'EMPLOYEE',
        ]);

        Employee::create([
            'name' => 'Dr. Khaled',
            'service_id' => $dental->id,
            'role' => 'EMPLOYEE',
        ]);
    }
}