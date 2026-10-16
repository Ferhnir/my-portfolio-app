<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AssetService
{
    public function __construct(private readonly AssetImageWriter $images) {}

    /**
     * @throws Throwable
     */
    public function store(Model $owner, UploadedFile $file, string $collection, ?int $sortOrder): Asset
    {
        $disk = (string) config('assets.disk');
        $archiveDisk = (string) config('assets.archive_disk');
        $written = $this->images->store($file, $this->directory($owner, $collection), $disk, $archiveDisk);

        try {
            $previous = null;
            $asset = DB::transaction(function () use ($owner, $file, $collection, $sortOrder, $disk, $archiveDisk, $written, &$previous): Asset {
                $attributes = [
                    'disk' => $disk,
                    'archive_disk' => $archiveDisk,
                    'path' => $written['original'],
                    'display_path' => $written['display'],
                    'thumb_path' => $written['thumb'],
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];

                if (in_array($collection, Asset::SINGLE_COLLECTIONS, true)) {
                    $existing = $owner->assets()->where('collection', $collection)->first();

                    if ($existing instanceof Asset) {
                        $previous = $this->files($existing);
                        $existing->update($attributes);

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
        } catch (Throwable $exception) {
            $this->forget($disk, $archiveDisk, $written['display'], $written['thumb'], $written['original']);

            throw $exception;
        }

        if (is_array($previous)) {
            $this->forget($previous['disk'], $previous['archive_disk'], $previous['display'], $previous['thumb'], $previous['original']);
        }

        return $asset;
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
        $files = $this->files($asset);

        DB::transaction(fn () => $asset->delete());

        $this->forget($files['disk'], $files['archive_disk'], $files['display'], $files['thumb'], $files['original']);
    }

    /**
     * @return array{disk: string, archive_disk: string, original: ?string, display: ?string, thumb: ?string}
     */
    private function files(Asset $asset): array
    {
        return [
            'disk' => $asset->disk,
            'archive_disk' => $asset->archive_disk ?: $asset->disk,
            'original' => $asset->path,
            'display' => $asset->display_path,
            'thumb' => $asset->thumb_path,
        ];
    }

    private function forget(string $disk, string $archiveDisk, ?string $display, ?string $thumb, ?string $original): void
    {
        if ($display) {
            Storage::disk($disk)->delete($display);
        }

        if ($thumb) {
            Storage::disk($disk)->delete($thumb);
        }

        if ($original) {
            Storage::disk($archiveDisk)->delete($original);
        }
    }

    private function directory(Model $owner, string $collection): string
    {
        return 'assets/'.$owner->getMorphClass().'/'.$owner->getKey().'/'.$collection;
    }
}
