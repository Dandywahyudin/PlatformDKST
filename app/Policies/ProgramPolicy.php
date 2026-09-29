<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($ability === 'delete') {
            return null;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('programs.view');
    }

    public function view(User $user, Program $program): bool
    {
        return $user->hasPermission('programs.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('programs.create');
    }

    public function update(User $user, Program $program): bool
    {
        return $user->hasPermission('programs.update');
    }

    public function delete(User $user, Program $program): bool
    {
        return $user->hasPermission('programs.delete') && in_array($program->status, [Program::STATUS_DRAFT, Program::STATUS_REJECTED]);
    }

    public function submit(User $user, Program $program): bool
    {
        return in_array($program->status, [Program::STATUS_DRAFT, Program::STATUS_REJECTED]);
    }

    public function approve(User $user, Program $program): bool
    {
        return ($user->hasPermission('programs.approve') || $user->hasPermission('approvals.process')) && in_array($program->status, [Program::STATUS_SUBMITTED, Program::STATUS_UNDER_REVIEW]);
    }

    public function reject(User $user, Program $program): bool
    {
        return ($user->hasPermission('programs.reject') || $user->hasPermission('approvals.process')) && in_array($program->status, [Program::STATUS_SUBMITTED, Program::STATUS_UNDER_REVIEW]);
    }
}
