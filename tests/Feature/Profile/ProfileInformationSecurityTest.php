<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileInformationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_email_without_changing_name(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'password' => Hash::make('CurrentPassword123!'),
        ]);

        $this->actingAs($user)
            ->put(route('user-profile-information.update'), [
                'email' => 'new@example.com',
                'current_password' => 'CurrentPassword123!',
                'name' => 'Hacked Name',
                'role' => 'admin',
                'is_super_admin' => 1,
                'admin_level' => 100,
                'admin_permissions' => ['users.manage'],
                'is_active' => 0,
            ])
            ->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('new@example.com', $user->email);
        $this->assertSame('Original Name', $user->name);
        $this->assertNotSame('admin', $user->role);
        $this->assertFalse((bool) $user->is_super_admin);
        $this->assertNotSame(100, (int) $user->admin_level);
        $this->assertNotContains(
            'users.manage',
            $user->admin_permissions ?? []
        );
    }

    public function test_wrong_password_prevents_email_change(): void
    {
        $user = User::factory()->create([
            'email' => 'original@example.com',
            'password' => Hash::make('CurrentPassword123!'),
        ]);

        $this->actingAs($user)
            ->put(route('user-profile-information.update'), [
                'email' => 'new@example.com',
                'current_password' => 'WrongPassword123!',
            ])
            ->assertSessionHasErrorsIn(
                'updateProfileInformation',
                ['current_password']
            );

        $this->assertSame(
            'original@example.com',
            $user->fresh()->email
        );
    }
}
