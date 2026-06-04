<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Greenhouse;
use App\Models\MelonVariety;

class SalesCreateTest extends TestCase
{
    public function test_form_submission_with_pay_later_redirects_to_kasir()
    {
        $user = User::where('role', 'admin')->first();
        $greenhouse = Greenhouse::first();
        $variety = MelonVariety::first();

        $response = $this->actingAs($user)->post(
            route('sales.store', $greenhouse),
            [
                'buyer_name' => 'Test Buyer',
                'sale_items' => [
                    [
                        'melon_variety_id' => $variety->id,
                        'weight_kg' => 5.5,
                        'price_per_kg' => 50000,
                    ]
                ],
                'action' => 'pay_later',
            ]
        );

        $response->assertRedirect(route('sales.greenhouse', $greenhouse));
        $this->assertDatabaseHas('sales', [
            'buyer_name' => 'Test Buyer',
            'greenhouse_id' => $greenhouse->id,
        ]);
    }

    public function test_form_submission_with_pay_and_print_redirects_to_print()
    {
        $user = User::where('role', 'admin')->first();
        $greenhouse = Greenhouse::first();
        $variety = MelonVariety::first();

        $response = $this->actingAs($user)->post(
            route('sales.store', $greenhouse),
            [
                'buyer_name' => 'Test Buyer 2',
                'sale_items' => [
                    [
                        'melon_variety_id' => $variety->id,
                        'weight_kg' => 3.0,
                        'price_per_kg' => 40000,
                    ]
                ],
                'action' => 'pay_and_print',
            ]
        );

        $sale = \App\Models\Sale::where('buyer_name', 'Test Buyer 2')->first();
        $response->assertRedirect(route('sales.print', $sale));
        $response->assertSessionHas('auto_print', true);
    }

    public function test_form_submission_without_action_fails_validation()
    {
        $user = User::where('role', 'admin')->first();
        $greenhouse = Greenhouse::first();
        $variety = MelonVariety::first();

        $response = $this->actingAs($user)->post(
            route('sales.store', $greenhouse),
            [
                'buyer_name' => 'Test Buyer 3',
                'sale_items' => [
                    [
                        'melon_variety_id' => $variety->id,
                        'weight_kg' => 3.0,
                        'price_per_kg' => 40000,
                    ]
                ],
            ]
        );

        $response->assertSessionHasErrors('action');
    }
}
