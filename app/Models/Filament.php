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
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'spool_price' => 'decimal:2',
            'spool_weight_grams' => 'decimal:2',
            'cost_per_gram' => 'decimal:4',
            'is_active' => 'boolean',
        ];
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
