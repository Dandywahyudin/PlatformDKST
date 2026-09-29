<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    /**
     * Get roles assigned to user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    /**
     * Check if user has a specific role.
     *
     * @param  string|array<int, string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        $roleList = is_array($roles) ? $roles : [$roles];

        return $this->roles->contains(function (Role $role) use ($roleList) {
            return in_array(strtolower($role->slug), array_map('strtolower', $roleList), true)
                || in_array(strtolower($role->name), array_map('strtolower', $roleList), true);
        });
    }

    /**
     * Check if user is an Admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'ADMIN', 'Super Admin']);
    }

    /**
     * Check if user is a Director.
     */
    public function isDirector(): bool
    {
        return $this->hasRole(['director', 'DIRECTOR', 'Direktur', 'Director', 'eksekutif', 'executive', 'pimpinan']);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isDirector() && in_array($permissionSlug, [
            'dashboard.view',
            'programs.view',
            'programs.approve',
            'programs.reject',
            'approvals.view',
            'approvals.process',
            'monev.view',
            'monev.update',
            'impact.view',
            'impact.manage',
            'documents.view',
            'documents.download',
            'services.view',
            'services.update',
            'audit_logs.view',
            'notifications.view',
        ])) {
            return true;
        }

        return $this->roles->flatMap(function (Role $role) {
            return $role->permissions;
        })->contains('slug', $permissionSlug);
    }

    /**
     * Assign a role to user.
     */
    public function assignRole(string|Role $role): self
    {
        $roleModel = is_string($role) ? Role::where('slug', strtolower($role))->firstOrFail() : $role;
        $this->roles()->syncWithoutDetaching([$roleModel->id]);

        return $this;
    }

    /**
     * Remove a role from user.
     */
    public function removeRole(string|Role $role): self
    {
        $roleModel = is_string($role) ? Role::where('slug', strtolower($role))->first() : $role;
        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
        }

        return $this;
    }

    /**
     * Sync roles for user.
     *
     * @param  array<int, int|string|Role>  $roles
     */
    public function syncRoles(array $roles): self
    {
        $roleIds = collect($roles)->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }
            if (is_numeric($role)) {
                return (int) $role;
            }

            return Role::where('slug', strtolower($role))->value('id');
        })->filter()->all();

        $this->roles()->sync($roleIds);

        return $this;
    }
}
