<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AssetImageWriter
{
    /**
     * @return array{original: string, display: string, thumb: string}
     */
    public function store(UploadedFile $file, string $directory, string $disk, string $archiveDisk): array
    {
        $source = $this->read($file);
        $source = $this->orient($source, $file);
        $display = $this->fit($source, (int) config('assets.display_max_edge'));
        $thumb = $this->fit($source, (int) config('assets.thumb_max_edge'));
        imagedestroy($source);

        try {
            $displayBytes = $this->encode($display, (int) config('assets.display_quality'));
            $maxBytes = (int) config('assets.display_max_bytes');

            if (strlen($displayBytes) > $maxBytes) {
                $displayBytes = $this->encode($display, 70);
            }

            $thumbBytes = $this->encode($thumb, (int) config('assets.thumb_quality'));
        } finally {
            imagedestroy($display);
            imagedestroy($thumb);
        }

        $name = (string) Str::uuid();
        $extension = $file->guessExtension() ?: 'bin';
        $original = $file->storeAs($directory, $name.'.'.$extension, $archiveDisk);

        if ($original === false) {
            throw ValidationException::withMessages([
                'file' => 'The image could not be stored.',
            ]);
        }

        $displayPath = $directory.'/'.$name.'-display.webp';
        $thumbPath = $directory.'/'.$name.'-thumb.webp';
        Storage::disk($disk)->put($displayPath, $displayBytes);
        Storage::disk($disk)->put($thumbPath, $thumbBytes);

        return [
            'original' => $original,
            'display' => $displayPath,
            'thumb' => $thumbPath,
        ];
    }

    private function read(UploadedFile $file): \GdImage
    {
        $contents = file_get_contents($file->getRealPath());
        $image = imagecreatefromstring($contents === false ? '' : $contents);

        if (! $image instanceof \GdImage) {
            throw ValidationException::withMessages([
                'file' => 'The image could not be read.',
            ]);
        }

        return $image;
    }

    private function orient(\GdImage $image, UploadedFile $file): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($file->getRealPath());
        $orientation = (int) ($exif['Orientation'] ?? 1);
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => null,
        };

        if (! $rotated instanceof \GdImage) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }

    private function fit(\GdImage $image, int $maxEdge): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $edge = max($width, $height);

        if ($edge <= $maxEdge || $edge === 0) {
            return $this->copy($image);
        }

        $scale = $maxEdge / $edge;
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        return $canvas;
    }

    private function copy(\GdImage $image): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $copy = imagecreatetruecolor($width, $height);
        imagealphablending($copy, false);
        imagesavealpha($copy, true);
        imagecopy($copy, $image, 0, 0, 0, 0, $width, $height);

        return $copy;
    }

    private function encode(\GdImage $image, int $quality): string
    {
        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        ob_start();
        $written = imagewebp($image, null, $quality);
        $bytes = ob_get_clean();

        if ($written !== true || ! is_string($bytes) || $bytes === '') {
            throw ValidationException::withMessages([
                'file' => 'The image could not be stored.',
            ]);
        }

        return $bytes;
    }
}
