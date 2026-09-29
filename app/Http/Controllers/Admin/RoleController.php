<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Role::class);

        $roles = Role::withCount(['permissions', 'users'])->get();
        $totalPermissions = Permission::count();

        return view('admin.roles.index', compact('roles', 'totalPermissions'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        Gate::authorize('create', Role::class);

        $groupedPermissions = Permission::all()->groupBy('module');

        return view('admin.roles.create', compact('groupedPermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'slug' => strtolower($validated['slug']),
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync($validated['permissions']);

            AuditLogService::log(
                AuditLog::MODULE_ROLES,
                AuditLog::ACTION_CREATE,
                "Membuat role baru: {$role->name} ({$role->slug}) dengan ".count($validated['permissions']).' hak akses',
                $role,
                null,
                $role->only(['name', 'slug', 'description'])
            );

            return $role;
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role {$role->name} berhasil dibuat.");
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): View
    {
        Gate::authorize('view', $role);

        $role->load(['permissions', 'users']);
        $groupedPermissions = $role->permissions->groupBy('module');

        return view('admin.roles.show', compact('role', 'groupedPermissions'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        Gate::authorize('update', $role);

        $role->load('permissions');
        $groupedPermissions = Permission::all()->groupBy('module');
        $currentPermissionIds = $role->permissions->pluck('id')->all();

        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'currentPermissionIds'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();
        $oldData = $role->only(['name', 'slug', 'description']);

        DB::transaction(function () use ($validated, $role, $oldData) {
            $role->update([
                'name' => $validated['name'],
                'slug' => strtolower($validated['slug']),
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync($validated['permissions']);

            AuditLogService::log(
                AuditLog::MODULE_ROLES,
                AuditLog::ACTION_UPDATE,
                "Memperbarui role: {$role->name} ({$role->slug})",
                $role,
                $oldData,
                $role->only(['name', 'slug', 'description'])
            );
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role {$role->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        Gate::authorize('delete', $role);

        $roleName = $role->name;

        DB::transaction(function () use ($role) {
            AuditLogService::log(
                AuditLog::MODULE_ROLES,
                AuditLog::ACTION_DELETE,
                "Menghapus role: {$role->name} ({$role->slug})",
                $role
            );

            $role->delete();
        });

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Role {$roleName} berhasil dihapus.");
    }
}
