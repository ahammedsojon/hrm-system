<?php

namespace App\Services\Document;

use App\Models\Document;
use App\Services\Media\MediaService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class DocumentService
{
    public function __construct(
        private readonly MediaService $mediaService,
    ) {}

    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Document::query()
            ->with(['employee', 'media'])
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('media', function ($query) use ($search) {
                            $query->where('original_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function find(int $id): Document
    {
        return Document::query()
            ->with(['employee', 'media'])
            ->findOrFail($id);
    }

    public function create(array $data, UploadedFile $file): Document
    {
        return DB::transaction(function () use ($data, $file) {
            $media = $this->mediaService->store($file, 'documents', 'document');

            return Document::query()
                ->create([
                    'employee_id' => $data['employee_id'],
                    'name' => $data['name'],
                    'media_id' => $media->id,
                ])
                ->load(['employee', 'media']);
        });
    }

    public function update(Document $document, array $data, ?UploadedFile $file = null, bool $removeFile = false): Document
    {
        return DB::transaction(function () use ($document, $data, $file, $removeFile) {
            $oldMedia = $document->media;

            if ($file) {
                $media = $this->mediaService->store($file, 'documents', 'document');
                $data['media_id'] = $media->id;
            }

            $document->update($data);

            if (($file || $removeFile) && $oldMedia && $oldMedia->id !== $document->media_id) {
                $this->mediaService->delete($oldMedia);
            }

            return $document->fresh(['employee', 'media']);
        });
    }

    public function delete(Document $document): void
    {
        $document->delete();
    }
}
