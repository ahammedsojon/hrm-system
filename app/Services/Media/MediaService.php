<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function store(
        UploadedFile $file,
        string $directory = 'uploads',
        ?string $collection = null,
        string $disk = 'public',
    ): Media {
        $path = $file->store($directory, $disk);

        return Media::query()->create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize() ?: 0,
            'collection' => $collection,
        ]);
    }

    public function delete(?Media $media): void
    {
        if (! $media) {
            return;
        }

        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
    }
}
