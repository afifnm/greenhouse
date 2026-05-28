<?php

namespace Database\Seeders;

use App\Models\MelonVariety;
use Illuminate\Database\Seeder;

class MelonVarietySeeder extends Seeder
{
    public function run(): void
    {
        MelonVariety::firstOrCreate(['slug' => 'sugar-honey'], [
            'name' => 'Sugar Honey',
            'slug' => 'sugar-honey',
            'description' => 'Melon rasa manis tinggi dan aroma harum',
        ]);
        MelonVariety::firstOrCreate(['slug' => 'dalmation'], [
            'name' => 'Dalmation',
            'slug' => 'dalmation',
            'description' => 'Melon varietas dalmation dengan kulit motif unik',
        ]);
        MelonVariety::firstOrCreate(['slug' => 'honey'], [
            'name' => 'Honey',
            'slug' => 'honey',
            'description' => 'Melon honey dengan tekstur lembut dan manis alami',
        ]);
    }
}
