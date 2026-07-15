<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\StoreDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Services\Document\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    public function index(Request $request)
    {
        $documents = $this->documentService->paginate(
            $request->string('search')->toString() ?: null,
            $request->integer('per_page', 15),
        );

        return DocumentResource::collection($documents);
    }

    public function store(StoreDocumentRequest $request): DocumentResource
    {
        $document = $this->documentService->create(
            $request->safe()->except('file'),
            $request->file('file'),
        );

        return new DocumentResource($document);
    }

    public function show(Document $document): DocumentResource
    {
        return new DocumentResource($this->documentService->find($document->id));
    }

    public function update(UpdateDocumentRequest $request, Document $document): DocumentResource
    {
        $document = $this->documentService->update(
            $document,
            $request->safe()->except(['file', 'remove_file']),
            $request->file('file'),
            $request->boolean('remove_file'),
        );

        return new DocumentResource($document);
    }

    public function destroy(Document $document): Response
    {
        $this->documentService->delete($document);

        return response()->noContent();
    }
}
