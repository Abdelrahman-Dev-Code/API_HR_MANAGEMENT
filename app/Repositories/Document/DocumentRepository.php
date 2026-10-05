<?php

namespace App\Repositories\Document;

use App\Models\Document;
use App\Repositories\Shared\BaseRepository;
use App\Services\Authorization\DataVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DocumentRepository extends BaseRepository implements DocumentRepositoryInterface
{
    public function __construct(private readonly DataVisibility $visibility)
    {
        parent::__construct(new Document());
    }

    protected function query(): Builder
    {
        $query = parent::query()->with('employee');
        $user = Auth::user();

        return $user ? $this->visibility->scope($query, $user, 'documents') : $query;
    }
}
