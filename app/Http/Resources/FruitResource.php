<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FruitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'tree_id'    => $this->tree_id,
            'condition'  => $this->condition,
            'grade'      => $this->grade,
            'weight'     => $this->weight,
            'notes'      => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
