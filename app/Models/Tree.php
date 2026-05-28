<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    use HasFactory;

    protected $fillable = ['greenhouse_id', 'melon_variety_id', 'tree_number', 'status'];

    public function greenhouse(): BelongsTo
    {
        return $this->belongsTo(Greenhouse::class);
    }

    public function variety(): BelongsTo
    {
        return $this->belongsTo(MelonVariety::class, 'melon_variety_id');
    }

    public function fruits(): HasMany
    {
        return $this->hasMany(Fruit::class);
    }
}
