<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const PERMISSIONS = [
        'products.manage' => 'إدارة المنتجات',
        'categories.manage' => 'إدارة التصنيفات',
        'locations.manage' => 'إدارة الفروع',
        'gallery.manage' => 'إدارة المعرض',
        'pages.manage' => 'إدارة الصفحات',
        'homepage.manage' => 'إدارة الصفحة الرئيسية',
        'messages.manage' => 'إدارة الرسائل',
        'analytics.view' => 'عرض التحليلات',
        'activities.view' => 'عرض سجل الأنشطة',
        'settings.manage' => 'إدارة إعدادات الموقع',
        'navigation.manage' => 'إدارة القائمة',
        'email-notifications.manage' => 'إدارة إشعارات البريد',
        'system-health.view' => 'عرض حالة النظام',
        'users.manage' => 'إدارة المستخدمين',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'admin_level' => 'integer',
            'admin_permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function adminActivities(): HasMany
    {
        return $this->hasMany(AdminActivity::class);
    }

    public function hasAdminPermission(string $permission): bool
    {
        if ($this->role !== 'admin' || ! $this->is_active) {
            return false;
        }

        if ($this->is_super_admin) {
            return true;
        }

        return in_array(
            $permission,
            $this->admin_permissions ?? [],
            true
        );
    }


    public function canManageUser(User $target): bool
    {
        if (! $this->hasAdminPermission('users.manage')) {
            return false;
        }

        // Only administrative accounts can be managed here.
        if ($target->role !== 'admin') {
            return false;
        }

        // The principal administrator is protected.
        if ($target->is_super_admin) {
            return false;
        }

        // Administrators cannot manage themselves.
        if ($this->is($target)) {
            return false;
        }

        return $this->is_super_admin
            || $this->admin_level > $target->admin_level;
    }

    public function canAssignLevel(int $level): bool
    {
        return $this->hasAdminPermission('users.manage')
            && $level >= 0
            && $level < 100
            && (
                $this->is_super_admin
                || $level < $this->admin_level
            );
    }


    public function canAssignPermissions(array $permissions): bool
    {
        if (! $this->hasAdminPermission('users.manage')) {
            return false;
        }

        foreach ($permissions as $permission) {
            if (
                ! is_string($permission)
                || ! array_key_exists($permission, self::PERMISSIONS)
                || ! $this->hasAdminPermission($permission)
            ) {
                return false;
            }
        }

        return true;
    }
}
