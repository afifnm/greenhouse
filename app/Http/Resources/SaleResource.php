<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'greenhouse_id' => $this->greenhouse_id,
            'buyer_name'    => $this->buyer_name,
            'total'         => $this->total,
            'sold_by'       => new UserResource($this->whenLoaded('user')),
            'items'         => SaleItemResource::collection($this->whenLoaded('items')),
            'created_at'    => $this->created_at,
        ];
    }
}
