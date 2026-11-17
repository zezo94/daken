<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\User;
use App\Services\AdminSessionRevoker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Str;











class UserManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => User::query()
                ->where('role', 'admin')
                ->orderByDesc('is_super_admin')
                ->orderByDesc('admin_level')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('admin.users.form', [
            'user' => null,
            'permissions' => User::PERMISSIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $data = $this->validatedData($request);

        DB::transaction(function () use ($request, $data): void {
            $actor = User::query()
                ->lockForUpdate()
                ->findOrFail($request->user()->id);

            abort_unless(
                $actor->hasAdminPermission('users.manage'),
                403
            );

            $this->ensureAssignable($actor, $data);

            $user = new User();

            $user->forceFill([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'admin',
                'is_super_admin' => false,
                'admin_level' => (int) $data['admin_level'],
                'admin_permissions' => $this->normalizePermissions(
                    $data['admin_permissions'] ?? []
                ),
                'is_active' => (bool) $data['is_active'],
            ]);

            $user->save();

            $this->recordActivity(
                $request,
                $actor,
                $user,
                'admin.created',
                [
                    'admin_level' => $user->admin_level,
                    'admin_permissions' => $user->admin_permissions,
                    'is_active' => $user->is_active,
                ]
            );
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم إنشاء المدير بنجاح.');
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('admin.users.form', [
            'user' => $user,
            'permissions' => User::PERMISSIONS,
        ]);
    }

    public function update(
        Request $request,
        User $user
    ): RedirectResponse {
        Gate::authorize('update', $user);

        $data = $this->validatedData($request, $user);

        DB::transaction(function () use ($request, $user, $data): void {
            $actor = User::query()
                ->lockForUpdate()
                ->findOrFail($request->user()->id);

            $target = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            abort_unless(
                $actor->canManageUser($target),
                403,
                'لا يمكنك إدارة هذا الحساب.'
            );

            $this->ensureAssignable($actor, $data);

            $oldLevel = (int) $target->admin_level;
            $oldPermissions = $this->normalizePermissions(
                $target->admin_permissions ?? []
            );
            $wasActive = (bool) $target->is_active;
            $oldEmail = (string) $target->email;
            $newLevel = (int) $data['admin_level'];
            $newPermissions = $this->normalizePermissions(
                $data['admin_permissions'] ?? []
            );
            $newActive = (bool) $data['is_active'];

            $passwordChanged = ! empty($data['password']);
            $levelChanged = $oldLevel !== $newLevel;
            $permissionsChanged = $oldPermissions !== $newPermissions;
            $deactivated = $wasActive && ! $newActive;
            $emailChanged = $oldEmail !== $data['email'];

            $securityChanged =
                $passwordChanged ||
                $emailChanged ||
                $levelChanged ||
                $permissionsChanged ||
                $deactivated;
            $before = [
                'admin_level' => $oldLevel,
                'admin_permissions' => $oldPermissions,
                'is_active' => $wasActive,
            ];

            $attributes = [
                'name' => $data['name'],
                'email' => $data['email'],
                'admin_level' => $newLevel,
                'admin_permissions' => $newPermissions,
                'is_active' => $newActive,
            ];

            if ($passwordChanged) {
                $attributes['password'] = $data['password'];
            }
            if ($securityChanged) {
                $attributes['remember_token'] = Str::random(60);
            }

            $target->forceFill($attributes)->save();

            // Invalidate sessions after security-sensitive changes.
            if ($securityChanged) {
                app(AdminSessionRevoker::class)
                    ->revokeForUser($target->id);
            }

            // Exactly one audit record per successful update.
            $this->recordActivity(
                $request,
                $actor,
                $target,
                'admin.updated',
                [
                    'before' => $before,
                    'after' => [
                        'admin_level' => (int) $target->admin_level,
                        'admin_permissions' => $this->normalizePermissions(
                            $target->admin_permissions ?? []
                        ),
                        'is_active' => (bool) $target->is_active,
                    ],
                    'password_changed' => $passwordChanged,
                    'level_changed' => $levelChanged,
                    'permissions_changed' => $permissionsChanged,
                    'email_changed' => $emailChanged,
                    'sessions_revoked' => $securityChanged,
                ]
            );
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم تحديث حساب المدير بنجاح.');
    }

    public function destroy(
        Request $request,
        User $user
    ): RedirectResponse {
        Gate::authorize('delete', $user);

        DB::transaction(function () use ($request, $user): void {
            $actor = User::query()
                ->lockForUpdate()
                ->findOrFail($request->user()->id);

            $target = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            abort_unless(
                $actor->canManageUser($target),
                403,
                'لا يمكنك تعطيل هذا الحساب.'
            );

            $wasActive = (bool) $target->is_active;

            $target->forceFill([
                'is_active' => false,
                'remember_token' => Str::random(60),
            ])->save();

            app(AdminSessionRevoker::class)
                ->revokeForUser($target->id);

            $this->recordActivity(
                $request,
                $actor,
                $target,
                'admin.deactivated',
                [
                    'was_active' => $wasActive,
                    'is_active' => false,
                ]
            );
        });

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'تم تعطيل حساب المدير مع الاحتفاظ بسجل أنشطته.'
            );
    }

    private function normalizePermissions(array $permissions): array
    {
        $permissions = array_values(
            array_unique($permissions)
        );

        sort($permissions, SORT_STRING);

        return $permissions;
    }

    private function recordActivity(
        Request $request,
        User $actor,
        User $target,
        string $action,
        array $details
    ): void {
        AdminActivity::query()->create([
            'user_id' => $actor->id,
            'action' => $action,
            'subject_type' => User::class,
            'subject_id' => $target->id,
            'details' => $details,
            'ip_address' => $request->ip(),
        ]);
    }

    private function validatedData(
        Request $request,
        ?User $user = null
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],

            'password' => [
                $user ? 'nullable' : 'required',
                'string',
                'min:12',
                'confirmed',
            ],

            'admin_level' => [
                'required',
                'integer',
                'between:0,99',
            ],

            'admin_permissions' => [
                'sometimes',
                'array',
                'max:' . count(User::PERMISSIONS),
            ],

            'admin_permissions.*' => [
                'string',
                'distinct',
                Rule::in(array_keys(User::PERMISSIONS)),
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);
    }

    private function ensureAssignable(
        User $actor,
        array $data
    ): void {
        if (! $actor->canAssignLevel(
            (int) $data['admin_level']
        )) {
            throw ValidationException::withMessages([
                'admin_level' =>
                    'لا يمكنك تعيين هذا المستوى الإداري.',
            ]);
        }

        if (! $actor->canAssignPermissions(
            $data['admin_permissions'] ?? []
        )) {
            throw ValidationException::withMessages([
                'admin_permissions' =>
                    'لا يمكنك منح صلاحيات لا تمتلكها.',
            ]);
        }
    }
}
