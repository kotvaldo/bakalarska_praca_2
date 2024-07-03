<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ControlPoint;
use App\Models\Drone;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\User::factory(10)->create();
        Drone::factory()->count(100)->create();
        ControlPoint::factory()->count(1000)->create();
    }
}
