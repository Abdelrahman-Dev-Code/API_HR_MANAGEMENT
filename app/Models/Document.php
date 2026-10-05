<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['document_name', 'document_type', 'file_path', 'file_size', 'expiry_date', 'notes', 'employee_id'])]
class Document extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'expiry_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
