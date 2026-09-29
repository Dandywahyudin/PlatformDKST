<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $query = User::with('roles')->latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), ['active', 'inactive'])) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('role')) {
            $roleSlug = $request->input('role');
            $query->whereHas('roles', function ($q) use ($roleSlug) {
                $q->where('slug', $roleSlug);
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        Gate::authorize('create', User::class);

        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'position' => $validated['position'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'status' => $validated['status'],
                'email_verified_at' => now(),
            ]);

            $user->roles()->sync($validated['roles']);

            AuditLogService::log(
                AuditLog::MODULE_USERS,
                AuditLog::ACTION_CREATE,
                "Membuat akun pengguna baru: {$user->name} ({$user->email})",
                $user,
                null,
                $user->only(['name', 'email', 'status', 'position'])
            );

            return $user;
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    /**
     * Display the specified user details.
     */
    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        $user->load(['roles.permissions', 'programsCreated', 'programsLead', 'tasks']);

        $recentActivities = AuditLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.users.show', compact('user', 'recentActivities'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        $user->load('roles');
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $oldData = $user->only(['name', 'email', 'status', 'position', 'phone']);

        DB::transaction(function () use ($validated, $user, $oldData) {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'position' => $validated['position'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'status' => $validated['status'],
            ];

            if (! empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);
            $user->roles()->sync($validated['roles']);

            AuditLogService::log(
                AuditLog::MODULE_USERS,
                AuditLog::ACTION_UPDATE,
                "Memperbarui data pengguna: {$user->name}",
                $user,
                $oldData,
                $user->only(['name', 'email', 'status', 'position', 'phone'])
            );
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Toggle active/inactive status of the specified user.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $oldStatus = $user->status;
        $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';

        $user->update(['status' => $newStatus]);

        AuditLogService::log(
            AuditLog::MODULE_USERS,
            AuditLog::ACTION_UPDATE,
            "Mengubah status pengguna {$user->name} dari {$oldStatus} menjadi {$newStatus}",
            $user,
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );

        return back()->with('success', "Status akun {$user->name} berhasil diubah menjadi {$newStatus}.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;

        DB::transaction(function () use ($user) {
            AuditLogService::log(
                AuditLog::MODULE_USERS,
                AuditLog::ACTION_DELETE,
                "Menghapus pengguna: {$user->name} ({$user->email})",
                $user
            );

            $user->delete();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Pengguna {$userName} berhasil dihapus.");
    }
}
