<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'greenhouse_id' => $this->greenhouse_id,
            'tree_number'   => $this->tree_number,
            'status'        => $this->status,
            'variety'       => new MelonVarietyResource($this->whenLoaded('variety')),
            'fruits_count'  => $this->whenCounted('fruits'),
            'created_at'    => $this->created_at,
        ];
    }
}
