<?php

namespace App\Policies;

use App\Models\ConsultationService;
use App\Models\User;

class ConsultationServicePolicy
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
        return $user->hasPermission('services.view');
    }

    public function view(User $user, ConsultationService $service): bool
    {
        return $user->hasPermission('services.view') || $user->id === $service->applicant_id || $user->id === $service->consultant_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('services.create');
    }

    public function update(User $user, ConsultationService $service): bool
    {
        return $user->hasPermission('services.update') || $user->id === $service->applicant_id;
    }

    public function delete(User $user, ConsultationService $service): bool
    {
        return $user->hasPermission('services.delete');
    }
}
