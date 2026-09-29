<?php

namespace App\Policies;

use App\Models\ProgramEvaluation;
use App\Models\User;

class ProgramEvaluationPolicy
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
        return $user->hasPermission('monev.view');
    }

    public function view(User $user, ProgramEvaluation $evaluation): bool
    {
        return $user->hasPermission('monev.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('monev.create');
    }

    public function update(User $user, ProgramEvaluation $evaluation): bool
    {
        return $user->hasPermission('monev.update');
    }

    public function delete(User $user, ProgramEvaluation $evaluation): bool
    {
        return $user->hasPermission('monev.delete');
    }
}
