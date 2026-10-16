<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AssetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assetable_type' => $this->assetable_type,
            'assetable_id' => $this->assetable_id,
            'collection' => $this->collection,
            'disk' => $this->disk,
            'path' => $this->path,
            'url' => $this->publicUrl($this->display_path) ?? $this->publicUrl($this->path),
            'thumb_url' => $this->publicUrl($this->thumb_path)
                ?? $this->publicUrl($this->display_path)
                ?? $this->publicUrl($this->path),
            'original_name' => $this->original_name,
            'mime' => $this->mime,
            'size' => $this->size,
            'sort_order' => $this->sort_order,
        ];
    }

    private function publicUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return Storage::disk($this->disk)->url($path);
    }
}
