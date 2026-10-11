<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filament extends Model
{
    protected $fillable = [
        'name',
        'spool_price',
        'spool_weight_grams',
        'cost_per_gram',
        'color_hint',
        'available_colors',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'spool_price' => 'decimal:2',
            'spool_weight_grams' => 'decimal:2',
            'cost_per_gram' => 'decimal:4',
            'available_colors' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<int, array{name: string, in_stock: bool}>
     */
    public function colorInventory(): array
    {
        return collect($this->available_colors ?? [])
            ->map(function ($color): ?array {
                if (is_string($color)) {
                    $name = trim($color);

                    return $name === '' ? null : ['name' => $name, 'in_stock' => true];
                }

                if (! is_array($color) || ! is_string($color['name'] ?? null)) {
                    return null;
                }

                $name = trim($color['name']);

                return $name === '' ? null : [
                    'name' => $name,
                    'in_stock' => (bool) ($color['in_stock'] ?? false),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public function hasManagedColorInventory(): bool
    {
        return $this->available_colors !== null;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function calculateGramCost(float $spoolPrice, float $spoolWeightGrams = 1000): float
    {
        $weight = $spoolWeightGrams > 0 ? $spoolWeightGrams : 1000;

        return round($spoolPrice / $weight, 4);
    }
}
