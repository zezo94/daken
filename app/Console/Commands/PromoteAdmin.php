<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PromoteAdmin extends Command
{
    protected $signature = 'daken:promote-admin
        {email : Existing user email}
        {--level=20 : Administrative level from 0 to 99}
        {--all-permissions : Grant all available section permissions}
        {--super-admin : Assign the protected principal administrator}';

    protected $description = 'Safely promote an existing DAKEN user to administrator';

    public function handle(): int
    {
        $email = trim((string) $this->argument('email'));

        $validator = Validator::make(
            [
                'email' => $email,
                'level' => $this->option('level'),
            ],
            [
                'email' => ['required', 'email'],
                'level' => ['required', 'integer', 'between:0,99'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user) {
            $this->error('User not found.');

            return self::FAILURE;
        }

        $makeSuperAdmin = (bool) $this->option('super-admin');
        $grantAll = (bool) $this->option('all-permissions');

        if ($makeSuperAdmin && $grantAll) {
            $this->error(
                'Do not combine --super-admin with --all-permissions.'
            );

            return self::FAILURE;
        }

        if ($user->is_super_admin && ! $makeSuperAdmin) {
            $this->error(
                'The principal administrator cannot be demoted using this command.'
            );

            return self::FAILURE;
        }

        if ($makeSuperAdmin) {
            $existingSuperAdmin = User::query()
                ->where('is_super_admin', true)
                ->whereKeyNot($user->id)
                ->exists();

            if ($existingSuperAdmin) {
                $this->error(
                    'A principal administrator already exists. Operation denied.'
                );

                return self::FAILURE;
            }
        }

        $level = $makeSuperAdmin
            ? 100
            : (int) $this->option('level');

        $permissions = $makeSuperAdmin || $grantAll
            ? array_keys(User::PERMISSIONS)
            : ($user->role === 'admin'
                ? ($user->admin_permissions ?? [])
                : []);

        $this->table(
            ['Field', 'Value'],
            [
                ['Email', $user->email],
                ['Current role', $user->role],
                ['New role', 'admin'],
                ['New level', (string) $level],
                ['Super Admin', $makeSuperAdmin ? 'YES' : 'NO'],
                ['Permission count', (string) count($permissions)],
                ['Active', 'YES'],
            ]
        );

        if (! $this->confirm('Apply these administrative changes?', false)) {
            $this->warn('Operation cancelled.');

            return self::FAILURE;
        }

        try {
            DB::transaction(function () use (
                $user,
                $makeSuperAdmin,
                $level,
                $permissions
            ): void {
                $lockedUser = User::query()
                    ->lockForUpdate()
                    ->findOrFail($user->id);

                if ($lockedUser->is_super_admin && ! $makeSuperAdmin) {
                    throw new \RuntimeException(
                        'Cannot demote the principal administrator.'
                    );
                }

                if (
                    $makeSuperAdmin
                    && User::query()
                        ->where('is_super_admin', true)
                        ->whereKeyNot($lockedUser->id)
                        ->exists()
                ) {
                    throw new \RuntimeException(
                        'Another principal administrator already exists.'
                    );
                }

                $lockedUser->forceFill([
                    'role' => 'admin',
                    'is_super_admin' => $makeSuperAdmin,
                    'admin_level' => $level,
                    'admin_permissions' => $permissions,
                    'is_active' => true,
                ])->save();
            });
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Administrator account updated successfully.');

        return self::SUCCESS;
    }
}
