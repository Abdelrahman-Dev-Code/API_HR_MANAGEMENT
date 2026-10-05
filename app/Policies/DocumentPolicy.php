<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy extends BaseResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user, 'documents');
    }

    public function view(User $user, Document $document): bool
    {
        return $this->canView($user, 'documents');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'documents.manage');
    }

    public function update(User $user, Document $document): bool
    {
        return $this->hasPermission($user, 'documents.manage');
    }

    public function delete(User $user, Document $document): bool
    {
        return $this->hasPermission($user, 'documents.manage');
    }

    public function download(User $user, Document $document): bool
    {
        return $this->canView($user, 'documents');
    }
}
