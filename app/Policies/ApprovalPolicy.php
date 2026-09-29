<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;

class ApprovalPolicy
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
        return $user->hasPermission('approvals.view');
    }

    public function view(User $user, Approval $approval): bool
    {
        return $user->hasPermission('approvals.view') || $user->id === $approval->requested_by;
    }

    public function process(User $user, Approval $approval): bool
    {
        return $user->hasPermission('approvals.process') && $approval->status === Approval::STATUS_PENDING;
    }
}
