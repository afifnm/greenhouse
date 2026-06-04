<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GreenhouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'code'         => $this->code,
            'description'  => $this->description,
            'is_active'    => $this->is_active,
            'total_trees'  => $this->whenNotNull($this->trees_count),
            'alive_trees'  => $this->whenNotNull($this->alive_trees_count),
            'sales_count'  => $this->whenNotNull($this->sales_count),
            'fruits_sold'  => $this->whenNotNull($this->fruits_sold_count),
            'revenue'      => (float) ($this->sales_sum_total ?? 0),
            'total_weight' => (float) ($this->sale_items_sum_weight_kg ?? 0),
            'created_at'   => $this->created_at,
        ];
    }
}
