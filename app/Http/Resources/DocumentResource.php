<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_name' => $this->document_name,
            'document_type' => $this->document_type,
            'file_size' => $this->file_size,
            'download_url' => route('documents.download', ['document' => $this->id]),
            'expiry_date' => $this->expiry_date?->toDateString(),
            'notes' => $this->notes,
            'employee_id' => $this->employee_id,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
