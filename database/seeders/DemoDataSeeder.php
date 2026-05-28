<?php

namespace Database\Seeders;

use App\Models\Greenhouse;
use App\Models\MelonVariety;
use App\Models\Tree;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin@greenhouse.id'], [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $manager = User::firstOrCreate(['email' => 'manager@greenhouse.id'], [
            'name' => 'Budi Santoso',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        $gh1 = Greenhouse::firstOrCreate(['code' => 'GH-001'], [
            'name' => 'Greenhouse Utama',
            'description' => 'Greenhouse utama dengan kapasitas besar',
            'is_active' => true,
        ]);

        $gh2 = Greenhouse::firstOrCreate(['code' => 'GH-002'], [
            'name' => 'Greenhouse Timur',
            'description' => 'Greenhouse gedung timur',
            'is_active' => true,
        ]);

        $gh3 = Greenhouse::firstOrCreate(['code' => 'GH-003'], [
            'name' => 'Greenhouse Barat',
            'description' => 'Greenhouse gedung barat',
            'is_active' => true,
        ]);

        $manager->greenhouses()->syncWithoutDetaching([$gh1->id, $gh2->id]);

        if ($gh1->trees()->count() === 0) {
            $varieties = MelonVariety::all();
            for ($i = 1; $i <= 20; $i++) {
                Tree::create([
                    'greenhouse_id' => $gh1->id,
                    'melon_variety_id' => $varieties->random()->id,
                    'tree_number' => sprintf('GH-001-%04d', $i),
                    'status' => $i <= 18 ? 'alive' : 'dead',
                ]);
            }
        }
    }
}
