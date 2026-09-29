<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('documents.view');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('documents.upload');
    }

    public function download(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.download');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.delete') || $user->id === $document->uploaded_by;
    }
}
