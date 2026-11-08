<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminProfilePasswordSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_password_change_revokes_old_sessions(): void
    {
        $admin = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $admin->forceFill([
            'role' => 'admin',
            'is_active' => true,
        ])->save();

        $oldSessionId = Str::random(40);

        DB::table('sessions')->insert([
            'id' => $oldSessionId,
            'user_id' => $admin->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'DAKEN Security Test',
            'payload' => base64_encode('test'),
            'last_activity' => time(),
        ]);

        $this->actingAs($admin)
            ->put(route('user-password.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertSessionHasNoErrors();

        $admin->refresh();

        $this->assertTrue(
            Hash::check('NewPassword123!', $admin->password)
        );

        $this->assertFalse(
            Hash::check('OldPassword123!', $admin->password)
        );

        $this->assertDatabaseMissing('sessions', [
            'id' => $oldSessionId,
        ]);
    }
}
