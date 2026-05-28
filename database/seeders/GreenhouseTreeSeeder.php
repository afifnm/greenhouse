<?php

namespace Database\Seeders;

use App\Models\Fruit;
use App\Models\Greenhouse;
use App\Models\MelonVariety;
use App\Models\Tree;
use Illuminate\Database\Seeder;

class GreenhouseTreeSeeder extends Seeder
{
    public function run(): void
    {
        $varieties = MelonVariety::all();

        if ($varieties->isEmpty()) {
            $this->command->warn('No melon varieties found. Please run MelonVarietySeeder first.');
            return;
        }

        $greenhouses = Greenhouse::all();

        if ($greenhouses->isEmpty()) {
            $this->command->warn('No greenhouses found. Please create greenhouses first.');
            return;
        }

        foreach ($greenhouses as $gh) {
            $this->seedGreenhouse($gh, $varieties);
        }
    }

    private function seedGreenhouse(Greenhouse $gh, $varieties): void
    {
        $this->command->info("Seeding {$gh->name}...");

        // Clear existing data for this greenhouse
        $gh->trees()->delete();

        $totalTrees = 1500;
        $deadCount = 150;
        $aliveCount = 1350;

        $trees = [];

        // Generate tree data
        for ($i = 1; $i <= $totalTrees; $i++) {
            $treeNumber = sprintf('%s-%04d', $gh->code, $i);
            $status = $i <= $aliveCount ? 'alive' : 'dead';

            $trees[] = [
                'greenhouse_id' => $gh->id,
                'melon_variety_id' => $varieties->random()->id,
                'tree_number' => $treeNumber,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Batch insert trees for performance
        foreach (array_chunk($trees, 500) as $chunk) {
            Tree::insert($chunk);
        }

        $this->command->info("  -> Created {$totalTrees} trees ({$aliveCount} alive, {$deadCount} dead)");

        // Get alive trees and add fruits
        $aliveTrees = Tree::where('greenhouse_id', $gh->id)
            ->where('status', 'alive')
            ->get();

        $fruits = [];
        $fruitCount = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'rotten' => 0];

        foreach ($aliveTrees as $tree) {
            // Each alive tree has 1-3 fruits
            $fruitCountPerTree = rand(1, 3);

            for ($j = 0; $j < $fruitCountPerTree; $j++) {
                // 30% chance rotten, 70% chance good
                $isRotten = rand(1, 100) <= 30;

                if ($isRotten) {
                    $condition = 'rotten';
                    $grade = null;
                    $weight = null;
                } else {
                    $condition = 'good';
                    // Random grade: A, B, C, D with weighted probability
                    // A: 20%, B: 30%, C: 35%, D: 15%
                    $rand = rand(1, 100);
                    if ($rand <= 20) {
                        $grade = 'A';
                    } elseif ($rand <= 50) {
                        $grade = 'B';
                    } elseif ($rand <= 85) {
                        $grade = 'C';
                    } else {
                        $grade = 'D';
                    }
                    // Random weight: 0.5 - 3.0 kg
                    $weight = round(mt_rand(50, 300) / 100, 2);
                }

                $fruits[] = [
                    'tree_id' => $tree->id,
                    'condition' => $condition,
                    'grade' => $grade,
                    'weight' => $weight,
                    'notes' => $isRotten ? 'Buah busuk' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($isRotten) {
                    $fruitCount['rotten']++;
                } else {
                    $fruitCount[$grade]++;
                }
            }
        }

        // Batch insert fruits for performance
        foreach (array_chunk($fruits, 500) as $chunk) {
            Fruit::insert($chunk);
        }

        $totalFruits = array_sum($fruitCount);
        $goodFruits = $totalFruits - $fruitCount['rotten'];

        $this->command->info("  -> Created {$totalFruits} fruits ({$goodFruits} good, {$fruitCount['rotten']} rotten)");
        $this->command->info("     Grade A: {$fruitCount['A']}, Grade B: {$fruitCount['B']}, Grade C: {$fruitCount['C']}, Grade D: {$fruitCount['D']}");
    }
}
