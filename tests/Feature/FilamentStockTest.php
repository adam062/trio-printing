<?php

namespace Tests\Feature;

use App\Models\Filament;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_colors_and_disable_an_entire_filament(): void
    {
        $filament = Filament::create([
            'name' => 'PLA+',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($admin)->get(route('admin.filaments.index'))
            ->assertOk()
            ->assertSee('ألوان الخامة ومخزون كل لون');

        $this->actingAs($admin)->put(route('admin.filaments.update', $filament), [
            'name' => $filament->name,
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'is_active' => '0',
            'colors' => [
                ['name' => 'أسود', 'in_stock' => '1'],
                ['name' => 'أبيض'],
            ],
        ])->assertRedirect();

        $filament->refresh();

        $this->assertFalse($filament->is_active);
        $this->assertSame([
            ['name' => 'أسود', 'in_stock' => true],
            ['name' => 'أبيض', 'in_stock' => false],
        ], $filament->colorInventory());
    }

    public function test_admin_can_remove_a_filament_without_deleting_linked_products(): void
    {
        $filament = Filament::create([
            'name' => 'PLA+ removable',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'is_active' => true,
        ]);
        $product = Product::create([
            'name' => 'Existing product',
            'filament_id' => $filament->id,
            'weight_grams' => 80,
            'base_price' => 60,
            'multiplier' => 4,
            'price' => 240,
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($admin)->get(route('admin.filaments.index'))
            ->assertOk()
            ->assertSee(route('admin.filaments.destroy', $filament), false)
            ->assertSee('حذف نوع الخامة', false);

        $this->actingAs($admin)->delete(route('admin.filaments.destroy', $filament))
            ->assertRedirect();

        $this->assertDatabaseMissing('filaments', ['id' => $filament->id]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'filament_id' => null,
        ]);
    }

    public function test_custom_quote_rejects_an_out_of_stock_color(): void
    {
        $filament = Filament::create([
            'name' => 'PLA+',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'available_colors' => [['name' => 'أبيض', 'in_stock' => false]],
            'is_active' => true,
        ]);

        $this->postJson(route('custom_quotes.store'), [
            'customer_name' => 'أحمد كمال',
            'phone' => '01282537892',
            'filament_id' => $filament->id,
            'color' => 'أبيض',
            'estimated_weight_grams' => 40,
        ])->assertUnprocessable()
            ->assertJson(['message' => 'نفدت ألوان الخامة المختارة حاليًا.']);
    }

    public function test_removing_every_managed_color_keeps_the_filament_out_of_stock(): void
    {
        $filament = Filament::create([
            'name' => 'PLA+',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'available_colors' => [['name' => 'أسود', 'in_stock' => true]],
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($admin)->put(route('admin.filaments.update', $filament), [
            'name' => $filament->name,
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'is_active' => '1',
        ])->assertRedirect();

        $filament->refresh();

        $this->assertTrue($filament->hasManagedColorInventory());
        $this->assertSame([], $filament->colorInventory());
    }

    public function test_custom_quote_accepts_a_color_that_is_in_stock(): void
    {
        $filament = Filament::create([
            'name' => 'PLA+',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'available_colors' => [
                ['name' => 'أسود', 'in_stock' => true],
                ['name' => 'أبيض', 'in_stock' => false],
            ],
            'is_active' => true,
        ]);

        $this->postJson(route('custom_quotes.store'), [
            'customer_name' => 'أحمد كمال',
            'phone' => '01282537892',
            'filament_id' => $filament->id,
            'color' => 'أسود',
            'estimated_weight_grams' => 40,
        ])->assertCreated();

        $this->assertDatabaseHas('custom_quotes', [
            'filament_id' => $filament->id,
            'color' => 'أسود',
        ]);
    }

    public function test_product_page_marks_stocked_and_sold_out_filament_colors(): void
    {
        $filament = Filament::create([
            'name' => 'PLA+',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'available_colors' => [
                ['name' => 'أسود', 'in_stock' => true],
                ['name' => 'أبيض', 'in_stock' => false],
            ],
            'is_active' => true,
        ]);
        $product = Product::create([
            'name' => 'Desk stand',
            'filament_id' => $filament->id,
            'weight_grams' => 80,
            'base_price' => 60,
            'multiplier' => 4,
            'price' => 240,
            'color_options' => ['لون قديم'],
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.show', $product));

        $response->assertOk()
            ->assertSee('أسود')
            ->assertSee('أبيض')
            ->assertSee('disabled', false)
            ->assertDontSee('لون قديم');
    }

    public function test_homepage_and_catalog_respect_filament_availability(): void
    {
        $availableFilament = Filament::create([
            'name' => 'PLA+ in stock',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'available_colors' => [['name' => 'أسود', 'in_stock' => true]],
            'is_active' => true,
        ]);
        $unavailableFilament = Filament::create([
            'name' => 'PETG out of stock',
            'spool_price' => 850,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.85,
            'available_colors' => [['name' => 'أبيض', 'in_stock' => true]],
            'is_active' => false,
        ]);

        foreach ([$availableFilament, $unavailableFilament] as $filament) {
            Product::create([
                'name' => $filament->name.' product',
                'filament_id' => $filament->id,
                'weight_grams' => 80,
                'base_price' => 60,
                'multiplier' => 4,
                'price' => 240,
                'is_active' => true,
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSee('اختار اللون')
            ->assertSee('نفد المخزون');

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('اختار اللون')
            ->assertSee('نفد المخزون');
    }
}
