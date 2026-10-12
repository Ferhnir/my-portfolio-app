<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TechStackGroupCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sort_order' => $this->sort_order,
            'group' => new TechStackGroupResource($this->whenLoaded('techStackGroup')),
            'category' => new TechStackCategoryResource($this->whenLoaded('techStackCategory')),
            'technologies' => TechStackPlacementResource::collection($this->whenLoaded('techStackPlacements')),
        ];
    }
}
