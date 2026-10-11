<?php

namespace Tests\Feature;

use App\Models\Filament;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductGalleryTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsSuperAdmin(): static
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'super_admin',
        ]);

        return $this->actingAs($admin);
    }

    /** @test */
    public function it_stores_multiple_uploaded_images_in_gallery(): void
    {
        Storage::fake('public');

        $filament = Filament::factory()->create();

        $this->actingAsSuperAdmin()
            ->post(route('admin.products.store'), [
                'name' => 'Test Multi-Image Product',
                'weight_grams' => 90,
                'multiplier' => 4,
                'filament_id' => $filament->id,
                'images' => [
                    UploadedFile::fake()->image('photo1.jpg'),
                    UploadedFile::fake()->image('photo2.jpg'),
                ],
            ])
            ->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Test Multi-Image Product')->firstOrFail();

        $gallery = $product->gallery();

        $this->assertCount(2, $gallery, 'Product should have 2 gallery images');
        $this->assertNotNull($product->image_path, 'image_path (cover) should be set');
        $this->assertNotNull($product->gallery_images, 'gallery_images should not be null');

        foreach ($gallery as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    /** @test */
    public function it_shows_gallery_thumbnails_on_product_detail_when_multiple_images_exist(): void
    {
        Storage::fake('public');

        $filament = Filament::factory()->create();
        $img1 = UploadedFile::fake()->image('a.jpg')->store('products', 'public');
        $img2 = UploadedFile::fake()->image('b.jpg')->store('products', 'public');

        $product = Product::create([
            'name' => 'Gallery Test',
            'weight_grams' => 50,
            'base_price' => 33.75,
            'multiplier' => 4,
            'price' => 135.00,
            'is_active' => true,
            'filament_id' => $filament->id,
            'image_path' => $img1,
            'gallery_images' => [$img1, $img2],
        ]);

        $this->get(route('catalog.show', $product))
            ->assertOk()
            ->assertSee('gallery-thumb-btn', false);
    }
}
