<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fruit extends Model
{
    use HasFactory;

    protected $fillable = ['tree_id', 'condition', 'grade', 'weight', 'notes'];
    protected $casts = ['weight' => 'decimal:2'];

    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }
}
