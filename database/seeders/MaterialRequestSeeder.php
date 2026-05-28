<?php

namespace Database\Seeders;

use App\Models\Greenhouse;
use App\Models\MaterialRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialRequestSeeder extends Seeder
{
    public function run(): void
    {
        $greenhouses = Greenhouse::all();

        if ($greenhouses->isEmpty()) {
            $this->command->warn('No greenhouses found.');
            return;
        }

        // Sample materials
        $materials = [
            ['Pupuk NPK', 50, 'kg'],
            ['Pupuk Organik', 25, 'kg'],
            ['Pestisida', 5, 'liter'],
            ['Mulsa Plastik', 10, 'roll'],
            ['Pipa Irigasi', 20, 'meter'],
            ['Sewa Traktor', 2, 'hari'],
            ['Bibit Melon Premium', 100, 'batang'],
            ['Ember Tanam', 15, 'pcs'],
            ['Cangkul', 5, 'pcs'],
            ['Selang Air', 30, 'meter'],
            ['Fungisida', 3, 'liter'],
            ['Kalsium Karbonat', 40, 'kg'],
            ['Polybag', 200, 'pcs'],
            ['Tali Rafia', 5, 'kg'],
            ['Hormon Grow', 2, 'liter'],
        ];

        $statuses = ['pending', 'pending', 'pending', 'fulfilled']; // 75% pending, 25% fulfilled

        $requests = [];

        foreach ($greenhouses as $gh) {
            // Get managers assigned to this greenhouse
            $managers = $gh->users;

            if ($managers->isEmpty()) {
                // Use any manager if none assigned
                $managers = User::where('role', 'manager')->where('is_active', true)->get();
            }

            if ($managers->isEmpty()) {
                $this->command->warn("No managers found for {$gh->name}");
                continue;
            }

            // Create 5-15 requests per greenhouse
            $count = rand(5, 15);

            for ($i = 0; $i < $count; $i++) {
                $material = $materials[array_rand($materials)];
                $status = $statuses[array_rand($statuses)];

                // Random date within last 30 days
                $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23));

                $requests[] = [
                    'greenhouse_id' => $gh->id,
                    'user_id' => $managers->random()->id,
                    'material_name' => $material[0],
                    'quantity' => rand(1, $material[1] * 2),
                    'unit' => $material[2],
                    'notes' => rand(0, 1) ? 'Butuh segera' : null,
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
        }

        // Batch insert
        foreach (array_chunk($requests, 100) as $chunk) {
            MaterialRequest::insert($chunk);
        }

        $pending = collect($requests)->where('status', 'pending')->count();
        $fulfilled = collect($requests)->where('status', 'fulfilled')->count();

        $this->command->info("Created " . count($requests) . " material requests ({$pending} pending, {$fulfilled} fulfilled)");
    }
}