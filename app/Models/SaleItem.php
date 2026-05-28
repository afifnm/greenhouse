<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'melon_variety_id', 'weight_kg', 'price_per_kg', 'subtotal'];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function melonVariety(): BelongsTo
    {
        return $this->belongsTo(MelonVariety::class);
    }
}
