<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['product_id', 'name', 'color', 'unit_price', 'weight_grams', 'quantity'];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'weight_grams' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
