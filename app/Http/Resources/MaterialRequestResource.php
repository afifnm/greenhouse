<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'greenhouse_id' => $this->greenhouse_id,
            'material_name' => $this->material_name,
            'quantity'      => $this->quantity,
            'unit'          => $this->unit,
            'notes'         => $this->notes,
            'status'        => $this->status,
            'requested_by'  => new UserResource($this->whenLoaded('user')),
            'created_at'    => $this->created_at,
        ];
    }
}
