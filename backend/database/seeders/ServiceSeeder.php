<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::create(['name' => 'General Consultation', 'is_open' => true]);
        Service::create(['name' => 'Dental', 'is_open' => true]);
        Service::create(['name' => 'Pediatrics', 'is_open' => false]);
    }
}