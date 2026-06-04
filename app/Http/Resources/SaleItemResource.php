<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'melon_variety'   => new MelonVarietyResource($this->whenLoaded('melonVariety')),
            'weight_kg'       => $this->weight_kg,
            'price_per_kg'    => $this->price_per_kg,
            'subtotal'        => $this->subtotal,
        ];
    }
}
