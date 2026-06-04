<?php

namespace Database\Seeders;

use App\Models\Greenhouse;
use App\Models\MelonVariety;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $greenhouses = Greenhouse::all();
        $users = User::whereIn('role', ['admin', 'manager'])->get();
        $varieties = MelonVariety::all();

        if ($greenhouses->isEmpty() || $users->isEmpty() || $varieties->isEmpty()) {
            $this->command->info('Tidak dapat membuat data penjualan dummy karena data master (greenhouse, user, atau varietas) kosong.');
            return;
        }

        $this->command->info('Membuat data penjualan dummy...');

        for ($i = 0; $i < 50; $i++) {
            $greenhouse = $greenhouses->random();
            $user = $users->random();

            $sale = Sale::create([
                'greenhouse_id' => $greenhouse->id,
                'user_id' => $user->id,
                'buyer_name' => 'Pelanggan ' . ($i + 1),
                'total' => 0, // Akan diupdate nanti
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);

            $totalSale = 0;
            $itemCount = rand(1, 4);

            for ($j = 0; $j < $itemCount; $j++) {
                $variety = $varieties->random();
                $weight = round(rand(10, 50) / 10, 1); // Berat antara 1.0 - 5.0 kg
                $price = rand(15000, 30000);
                $subtotal = $weight * $price;
                $totalSale += $subtotal;

                $sale->items()->create([
                    'melon_variety_id' => $variety->id,
                    'weight_kg' => $weight,
                    'price_per_kg' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total penjualan
            $sale->update(['total' => $totalSale]);
        }
    }
}
