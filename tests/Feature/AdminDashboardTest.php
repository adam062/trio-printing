<?php

namespace Tests\Feature;

use App\Models\Filament;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_visiting_admin_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_visiting_login_is_redirected_to_dashboard(): void
    {
        $this->actingAs(User::factory()->create([
            'is_admin' => true,
            'role' => 'super_admin',
        ]))->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_shows_filament_availability_and_uses_active_costs_for_its_estimate(): void
    {
        Filament::create([
            'name' => 'PLA+ A',
            'spool_price' => 500,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.5,
            'is_active' => true,
        ]);
        Filament::create([
            'name' => 'PLA+ B',
            'spool_price' => 900,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.9,
            'is_active' => true,
        ]);
        Filament::create([
            'name' => 'Inactive PETG',
            'spool_price' => 990,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.99,
            'is_active' => false,
        ]);
        $this->createPaidOrderWithWeight(200);

        $response = $this->actingAs(User::factory()->create([
            'is_admin' => true,
            'role' => 'super_admin',
        ]))->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('2 متاحة')
            ->assertSee('1 متوقفة')
            ->assertSee('3 إجماليًا')
            ->assertViewHas('materialCost', 140.0);
    }

    public function test_dashboard_does_not_invent_a_material_cost_when_no_filaments_are_active(): void
    {
        Filament::create([
            'name' => 'Inactive PETG',
            'spool_price' => 990,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.99,
            'is_active' => false,
        ]);
        $this->createPaidOrderWithWeight(200);

        $response = $this->actingAs(User::factory()->create([
            'is_admin' => true,
            'role' => 'super_admin',
        ]))->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewHas('activeFilamentsCount', 0)
            ->assertViewHas('materialCost', 0.0);
    }

    private function createPaidOrderWithWeight(int $weight): void
    {
        $order = Order::create([
            'order_number' => 'TRIO-TEST-001',
            'customer_name' => 'Dashboard Test',
            'phone' => '01000000000',
            'address' => 'Cairo',
            'subtotal' => 100,
            'delivery_fee' => 0,
            'total' => 100,
            'status' => 'paid',
        ]);

        $order->items()->create([
            'name' => 'Test print',
            'unit_price' => 100,
            'weight_grams' => $weight,
            'quantity' => 1,
        ]);
    }
}
