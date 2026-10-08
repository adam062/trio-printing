<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomQuote extends Model
{
    protected $fillable = [
        'quote_number',
        'customer_name',
        'phone',
        'email',
        'filament_id',
        'filament_name',
        'quality',
        'color',
        'file_path',
        'original_filename',
        'estimated_weight_grams',
        'multiplier',
        'estimated_price',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'estimated_weight_grams' => 'decimal:2',
            'multiplier' => 'decimal:2',
            'estimated_price' => 'decimal:2',
        ];
    }

    public function filament(): BelongsTo
    {
        return $this->belongsTo(Filament::class);
    }
}
