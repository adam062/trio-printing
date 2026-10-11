<?php

namespace Tests\Feature;

use App\Models\CustomQuote;
use App\Models\Filament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomQuoteDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploading_a_3mf_file_preserves_its_download_extension(): void
    {
        Storage::fake('public');
        $filament = Filament::create([
            'name' => 'PLA+ Test',
            'spool_price' => 750,
            'spool_weight_grams' => 1000,
            'cost_per_gram' => 0.75,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('custom_quotes.store'), [
            'customer_name' => 'Test Customer',
            'phone' => '01282537892',
            'filament_id' => $filament->id,
            'estimated_weight_grams' => 20,
            'file' => UploadedFile::fake()
                ->createWithContent('prototype.3mf', 'PK model data')
                ->mimeType('application/zip'),
        ]);

        $response->assertCreated();
        $quote = CustomQuote::query()->firstOrFail();
        $this->assertSame('prototype.3mf', $quote->original_filename);
        $this->assertStringEndsWith('.3mf', $quote->file_path);
        Storage::disk('public')->assertExists($quote->file_path);
    }

    public function test_admin_downloads_a_legacy_zip_stored_3mf_with_its_original_filename(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('custom_quotes/legacy.zip', 'PK model data');
        $quote = $this->createQuote('custom_quotes/legacy.zip', 'prototype.3mf');
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'super_admin']);

        $response = $this->actingAs($admin)->get(route('admin.custom_quotes.download', $quote));

        $response->assertDownload('prototype.3mf')
            ->assertHeader('Content-Type', 'model/3mf')
            ->assertStreamedContent('PK model data');
    }

    public function test_guest_cannot_download_a_customer_model(): void
    {
        $quote = $this->createQuote('custom_quotes/model.stl', 'model.stl');

        $this->get(route('admin.custom_quotes.download', $quote))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_without_quote_permission_cannot_download_a_customer_model(): void
    {
        $quote = $this->createQuote('custom_quotes/model.stl', 'model.stl');
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'manager',
            'permissions' => ['products'],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.custom_quotes.download', $quote))
            ->assertForbidden();
    }

    private function createQuote(string $filePath, string $originalFilename): CustomQuote
    {
        return CustomQuote::create([
            'quote_number' => 'CQ-'.strtoupper(str()->random(7)),
            'customer_name' => 'Test Customer',
            'phone' => '01282537892',
            'filament_name' => 'PLA+ Test',
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'estimated_weight_grams' => 20,
            'multiplier' => 4,
            'estimated_price' => 60,
            'status' => 'new',
        ]);
    }
}
