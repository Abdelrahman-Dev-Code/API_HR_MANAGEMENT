<?php

namespace App\Services\Document;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DocumentFileService
{
    /** @return array{file_path: string, file_size: int} */
    public function store(UploadedFile $file): array
    {
        $path = Storage::disk('local')->putFile('employee-documents', $file);

        if (! is_string($path)) {
            throw new RuntimeException('Unable to store the employee document.');
        }

        return [
            'file_path' => $path,
            'file_size' => (int) $file->getSize(),
        ];
    }

    public function delete(string $path): void
    {
        Storage::disk('local')->delete($path);
    }
}
