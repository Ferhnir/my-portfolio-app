<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudyBlockResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'case_study_section_id' => $this->case_study_section_id,
            'layout' => $this->layout,
            'sort_order' => $this->sort_order,
            'points' => CaseStudyPointResource::collection($this->whenLoaded('points')),
        ];
    }
}
