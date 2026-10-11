<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'details',
        'image_path',
        'gallery_images',
        'filament_id',
        'weight_grams',
        'dimensions',
        'color_options',
        'color_label',
        'base_price',
        'multiplier',
        'price',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'weight_grams' => 'decimal:2',
            'base_price' => 'decimal:2',
            'multiplier' => 'decimal:2',
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'gallery_images' => 'array',
            'color_options' => 'array',
        ];
    }

    public function filament(): BelongsTo
    {
        return $this->belongsTo(Filament::class);
    }

    /**
     * All product images in display order (falls back to the legacy single image).
     *
     * @return array<int, string>
     */
    public function gallery(): array
    {
        $gallery = array_values(array_filter($this->gallery_images ?? []));

        if ($gallery !== []) {
            return $gallery;
        }

        return $this->image_path ? [$this->image_path] : [];
    }

    public function coverImage(): ?string
    {
        return $this->gallery()[0] ?? null;
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function basePrice(float $weightGrams, ?float $gramCost = null): float
    {
        $cost = $gramCost !== null && $gramCost > 0 ? $gramCost : 0.675;

        return round($weightGrams * $cost, 2);
    }

    public static function calculatedPrice(float $weightGrams, float $multiplier, ?float $gramCost = null): float
    {
        return round(self::basePrice($weightGrams, $gramCost) * $multiplier, 2);
    }
}
