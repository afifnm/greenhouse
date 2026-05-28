<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MelonVarietySeeder::class,
            DemoDataSeeder::class,
            GreenhouseTreeSeeder::class,
            MaterialRequestSeeder::class,
        ]);
    }
}
