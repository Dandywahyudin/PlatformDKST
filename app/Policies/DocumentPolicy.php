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
        if ($user->isAdmin() || $user->isDirector()) {
            return true;
        }

        return $user->hasPermission('documents.view')
            || $user->hasPermission('programs.view')
            || $user->hasPermission('approvals.view')
            || $document->uploaded_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('documents.upload') || $user->hasPermission('programs.create') || $user->hasPermission('programs.update');
    }

    public function download(User $user, Document $document): bool
    {
        if ($user->isAdmin() || $user->isDirector()) {
            return true;
        }

        return $user->hasPermission('documents.download')
            || $user->hasPermission('documents.view')
            || $user->hasPermission('programs.view')
            || $user->hasPermission('approvals.view')
            || $document->uploaded_by === $user->id;
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.delete') || $user->id === $document->uploaded_by;
    }
}
