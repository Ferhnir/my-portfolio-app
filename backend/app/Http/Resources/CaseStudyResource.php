<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'client_name' => $this->client_name,
            'tagline' => $this->tagline,
            'summary' => $this->summary,
            'introduction' => $this->introduction,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'assets' => AssetResource::collection($this->whenLoaded('assets')),
            'sections' => CaseStudySectionResource::collection($this->whenLoaded('sections')),
        ];
    }
}
