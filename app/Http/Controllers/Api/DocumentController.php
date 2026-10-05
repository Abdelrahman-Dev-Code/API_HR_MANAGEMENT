<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\StoreDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Repositories\Document\DocumentRepositoryInterface;
use App\Services\Authorization\DataVisibility;
use App\Services\Document\DocumentFileService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documents,
        private readonly DocumentFileService $files,
        private readonly DataVisibility $visibility,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return DocumentResource::collection($this->documents->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->visibility->assertEmployeeInScope($request->user(), (int) $data['employee_id'], 'documents');
        $file = $data['file'];
        unset($data['file']);

        $storedFile = $this->files->store($file);

        try {
            $document = $this->documents->create([...$data, ...$storedFile]);
        } catch (Throwable $exception) {
            $this->files->delete($storedFile['file_path']);

            throw $exception;
        }

        return (new DocumentResource($document))->response()->setStatusCode(201);
    }

    public function show(int $document): DocumentResource
    {
        return new DocumentResource($this->documents->find($document));
    }

    public function update(UpdateDocumentRequest $request, int $document): DocumentResource
    {
        $model = $this->documents->find($document);
        $data = $request->validated();
        $employeeId = (int) ($data['employee_id'] ?? $model->employee_id);
        $this->visibility->assertEmployeeInScope($request->user(), $employeeId, 'documents');
        $file = $data['file'] ?? null;
        unset($data['file']);
        $storedFile = $file ? $this->files->store($file) : null;

        try {
            $updatedDocument = $this->documents->update($model, [
                ...$data,
                ...($storedFile ?? []),
            ]);
        } catch (Throwable $exception) {
            if ($storedFile) {
                $this->files->delete($storedFile['file_path']);
            }

            throw $exception;
        }

        if ($storedFile) {
            $this->files->delete($model->file_path);
        }

        return new DocumentResource($updatedDocument);
    }

    public function destroy(int $document): Response
    {
        $model = $this->documents->find($document);
        $this->documents->delete($model);

        return response()->noContent();
    }

    public function download(int $document): StreamedResponse
    {
        $model = $this->documents->find($document);
        $disk = Storage::disk('local');

        abort_unless($disk->exists($model->file_path), 404);

        $extension = pathinfo($model->file_path, PATHINFO_EXTENSION);
        $downloadName = Str::slug($model->document_name).($extension === '' ? '' : '.'.$extension);

        return $disk->download($model->file_path, $downloadName);
    }
}
