<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'location' => $this->location,
            'unit_code' => $this->unit_code,
            'description' => $this->description,
            'unit_price' => $this->unit_price,
        ];
    }

  
}
