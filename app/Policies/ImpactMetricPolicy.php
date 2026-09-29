<?php

namespace App\Policies;

use App\Models\User;

class ImpactMetricPolicy
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
        return $user->hasPermission('impact.view');
    }

    public function manage(User $user): bool
    {
        return $user->hasPermission('impact.manage');
    }
}
