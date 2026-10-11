<?php

namespace Tests\Feature;

use App\Models\Filament;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductGalleryAndColorLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_colors_are_limited_to_the_filament_inventory(): void
    {
        Storage::fake('public');
        $filament = Filament::create([
            'name' => 'PLA+ Linked',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'available_colors' => [
                ['name' => 'أسود', 'in_stock' => true],
                ['name' => 'أبيض', 'in_stock' => true],
            ],
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Linked Stand',
            'filament_id' => $filament->id,
            'weight_grams' => 80,
            'multiplier' => 4,
            'color_options' => ['أسود', 'لون غريب غير موجود بالخامة'],
        ])->assertRedirect();

        $product = Product::latest('id')->first();
        $this->assertSame(['أسود'], $product->color_options);
    }

    public function test_product_stores_multiple_images_and_uses_first_as_cover(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Gallery Product',
            'weight_grams' => 80,
            'multiplier' => 4,
            'images' => [
                UploadedFile::fake()->image('front.jpg'),
                UploadedFile::fake()->image('side.png'),
            ],
        ])->assertRedirect();

        $product = Product::latest('id')->first();
        $this->assertCount(2, $product->gallery());
        $this->assertSame($product->gallery()[0], $product->image_path);
        $this->assertSame($product->coverImage(), $product->image_path);
    }

    public function test_product_update_appends_multiple_images_to_existing_gallery(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Gallery Product',
            'weight_grams' => 80,
            'multiplier' => 4,
            'images' => [UploadedFile::fake()->image('front.jpg')],
        ])->assertRedirect();

        $product = Product::latest('id')->first();
        $originalCover = $product->coverImage();

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'Gallery Product',
            'weight_grams' => 80,
            'multiplier' => 4,
            'images' => [
                UploadedFile::fake()->image('side.jpg'),
                UploadedFile::fake()->image('back.png'),
            ],
        ])->assertRedirect();

        $product->refresh();
        $this->assertCount(3, $product->gallery());
        $this->assertSame($originalCover, $product->coverImage());
        Storage::disk('public')->assertExists($product->gallery()[1]);
        Storage::disk('public')->assertExists($product->gallery()[2]);
    }

    public function test_removing_the_cover_image_promotes_the_next_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Gallery Product',
            'weight_grams' => 80,
            'multiplier' => 4,
            'images' => [
                UploadedFile::fake()->image('front.jpg'),
                UploadedFile::fake()->image('side.png'),
            ],
        ])->assertRedirect();

        $product = Product::latest('id')->first();
        [$cover, $second] = $product->gallery();

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'Gallery Product',
            'weight_grams' => 80,
            'multiplier' => 4,
            'remove_images' => [$cover],
        ])->assertRedirect();

        $product->refresh();
        $this->assertSame([$second], $product->gallery());
        $this->assertSame($second, $product->image_path);
        Storage::disk('public')->assertMissing($cover);
    }

    public function test_order_rejects_a_color_not_linked_to_the_product(): void
    {
        $product = Product::create([
            'name' => 'Unmanaged color product',
            'weight_grams' => 80,
            'base_price' => 60,
            'multiplier' => 4,
            'price' => 240,
            'color_options' => ['أسود'],
            'is_active' => true,
        ]);

        $this->postJson(route('orders.store'), [
            'customer_name' => 'أحمد كمال',
            'phone' => '01282537892',
            'address' => 'القاهرة',
            'cart' => [['id' => $product->id, 'quantity' => 1, 'color' => 'أبيض']],
        ])->assertUnprocessable()
            ->assertJson(['message' => 'اللون المختار غير مرتبط بهذا المنتج.']);

        $this->postJson(route('orders.store'), [
            'customer_name' => 'أحمد كمال',
            'phone' => '01282537892',
            'address' => 'القاهرة',
            'cart' => [['id' => $product->id, 'quantity' => 1, 'color' => 'أسود']],
        ])->assertCreated();
    }

    public function test_admin_can_override_the_calculated_price_manually(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Manual Price Product',
            'weight_grams' => 80,
            'multiplier' => 4,
            'price' => 999.5,
        ])->assertRedirect();

        $product = Product::latest('id')->first();
        $this->assertSame(999.5, (float) $product->price);

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'Manual Price Product',
            'weight_grams' => 80,
            'multiplier' => 4,
            'price' => 1250,
        ])->assertRedirect();

        $this->assertSame(1250.0, (float) $product->refresh()->price);
    }

    public function test_price_falls_back_to_the_calculated_value_when_not_overridden(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Auto Price Product',
            'weight_grams' => 100,
            'multiplier' => 3,
        ])->assertRedirect();

        $product = Product::latest('id')->first();
        $expected = Product::calculatedPrice(100, 3, 0.675);
        $this->assertSame($expected, (float) $product->price);
    }
}
