<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudySectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'case_study_id' => $this->case_study_id,
            'title' => $this->title,
            'introduction' => $this->introduction,
            'sort_order' => $this->sort_order,
            'blocks' => CaseStudyBlockResource::collection($this->whenLoaded('blocks')),
        ];
    }
}
