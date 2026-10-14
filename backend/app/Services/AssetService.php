<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AssetService
{
    /**
     * @throws Throwable
     */
    public function store(Model $owner, UploadedFile $file, string $collection, ?int $sortOrder): Asset
    {
        return DB::transaction(function () use ($owner, $file, $collection, $sortOrder): Asset {
            $disk = (string) config('assets.disk');
            $path = $file->store($this->directory($owner, $collection), $disk);

            if ($path === false) {
                throw ValidationException::withMessages([
                    'file' => 'The image could not be stored.',
                ]);
            }

            $attributes = [
                'disk' => $disk,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];

            if (in_array($collection, Asset::SINGLE_COLLECTIONS, true)) {
                $existing = $owner->assets()->where('collection', $collection)->first();

                if ($existing instanceof Asset) {
                    $previousDisk = $existing->disk;
                    $previousPath = $existing->path;
                    $existing->update($attributes);
                    Storage::disk($previousDisk)->delete($previousPath);

                    return $existing->refresh();
                }
            }

            $asset = $owner->assets()->create([
                ...$attributes,
                'collection' => $collection,
                'sort_order' => $sortOrder ?? 0,
            ]);

            return $asset->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function update(Asset $asset, array $attributes): Asset
    {
        return DB::transaction(function () use ($asset, $attributes): Asset {
            $asset->update($attributes);

            return $asset->refresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(Asset $asset): void
    {
        DB::transaction(function () use ($asset): void {
            Storage::disk($asset->disk)->delete($asset->path);
            $asset->delete();
        });
    }

    private function directory(Model $owner, string $collection): string
    {
        return 'assets/'.$owner->getMorphClass().'/'.$owner->getKey().'/'.$collection;
    }
}
