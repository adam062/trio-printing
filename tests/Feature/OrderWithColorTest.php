<?php

namespace Tests\Feature;

use App\Models\Filament;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWithColorTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_high_tech_3d_printer_chamber()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('trio-printer-chamber');
        $response->assertSee('CORE-XY · 320 mm/s');
        $response->assertSee('201282537892');
    }

    public function test_product_page_displays_color_variant_pills_and_whatsapp_link()
    {
        $filament = Filament::create([
            'name' => 'PLA+ Lime',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Cyber Stand',
            'description' => 'حامل تجريبي',
            'filament_id' => $filament->id,
            'weight_grams' => 80,
            'base_price' => 60,
            'multiplier' => 4,
            'price' => 240,
            'color_options' => ['برتقالي نيون', 'أسود غير لامع', 'أخضر ليموني'],
            'color_label' => 'تخصيص لون الحامل',
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.show', $product));

        $response->assertStatus(200);
        $response->assertSee('color-pill-btn');
        $response->assertSee('برتقالي نيون');
        $response->assertSee('تخصيص لون الحامل');
        $response->assertSee('201282537892');
    }

    public function test_order_submission_with_variant_color_stores_color_and_displays_in_admin()
    {
        $filament = Filament::create([
            'name' => 'PLA+ Matte',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Desk Tray',
            'description' => 'صينية مكتبية',
            'filament_id' => $filament->id,
            'weight_grams' => 100,
            'base_price' => 75,
            'multiplier' => 4,
            'price' => 300,
            'color_options' => ['رمادي رخامي', 'أسود مطفي'],
            'color_label' => 'لون الصينية',
            'is_active' => true,
        ]);

        $response = $this->postJson(route('orders.store'), [
            'customer_name' => 'أحمد كمال',
            'phone' => '01282537892',
            'address' => 'التجمع الخامس، القاهرة',
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 2,
                    'color' => 'رمادي رخامي',
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['order_number', 'total']);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'أحمد كمال',
            'phone' => '01282537892',
        ]);

        $this->assertDatabaseHas('order_items', [
            'name' => 'Desk Tray',
            'color' => 'رمادي رخامي',
            'quantity' => 2,
        ]);

        // Check Admin orders display
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.local',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $adminResponse = $this->actingAs($admin)->get(route('admin.orders.index'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('رمادي رخامي');
        $adminResponse->assertSee('اللون:');
    }
}
