<?php

namespace Tests\Feature\Admin;

use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActivityViewerTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(
        array $permissions = [],
        bool $active = true
    ): User {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'admin',
            'is_super_admin' => false,
            'admin_level' => 20,
            'admin_permissions' => $permissions,
            'is_active' => $active,
        ])->save();

        return $user->fresh();
    }

    private function makeActivity(User $actor): AdminActivity
    {
        return AdminActivity::query()->create([
            'user_id' => $actor->id,
            'action' => 'admin.created',
            'subject_type' => User::class,
            'subject_id' => $actor->id,
            'details' => [
                'admin_level' => 20,
                'admin_permissions' => ['products.manage'],
                'is_active' => true,
            ],
            'ip_address' => '127.0.0.1',
        ]);
    }

    public function test_authorized_admin_can_view_activity_index(): void
    {
        $actor = $this->makeAdmin(['activities.view']);
        $activity = $this->makeActivity($actor);

        $this->actingAs($actor)
            ->get(route('admin.activities.index'))
            ->assertOk()
            ->assertSee('admin.created', false);
    }

    public function test_authorized_admin_can_view_activity_details(): void
    {
        $actor = $this->makeAdmin(['activities.view']);
        $activity = $this->makeActivity($actor);

        $this->actingAs($actor)
            ->get(route('admin.activities.show', $activity))
            ->assertOk();
    }

    public function test_admin_without_permission_cannot_view_index(): void
    {
        $actor = $this->makeAdmin(['products.manage']);

        $this->actingAs($actor)
            ->get(route('admin.activities.index'))
            ->assertForbidden();
    }

    public function test_admin_without_permission_cannot_view_details(): void
    {
        $actor = $this->makeAdmin(['products.manage']);
        $activity = $this->makeActivity($actor);

        $this->actingAs($actor)
            ->get(route('admin.activities.show', $activity))
            ->assertForbidden();
    }

    public function test_admin_without_permission_cannot_export(): void
    {
        $actor = $this->makeAdmin(['products.manage']);

        $this->actingAs($actor)
            ->get(route('admin.activities.export'))
            ->assertForbidden();
    }

    public function test_inactive_admin_cannot_view_activities(): void
    {
        $actor = $this->makeAdmin(
            permissions: ['activities.view'],
            active: false
        );

        $this->actingAs($actor)
            ->get(route('admin.activities.index'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }
    public function test_authorized_admin_can_export_csv(): void
    {
        $actor = $this->makeAdmin(['activities.view']);
        $this->makeActivity($actor);

        $response = $this->actingAs($actor)
            ->get(route('admin.activities.export'));

        $response->assertOk();

        $this->assertStringContainsString(
            'text/csv',
            $response->headers->get('Content-Type', '')
        );

        $this->assertStringContainsString(
            'admin.created',
            $response->streamedContent()
        );
    }

    public function test_activity_filter_returns_matching_actions(): void
    {
        $actor = $this->makeAdmin(['activities.view']);

        $this->makeActivity($actor);

        AdminActivity::query()->create([
            'user_id' => $actor->id,
            'action' => 'product.created',
            'subject_type' => User::class,
            'subject_id' => $actor->id,
            'details' => [],
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($actor)
            ->get(route('admin.activities.index', [
                'action' => 'admin.created',
            ]))
            ->assertOk()
            ->assertViewHas('activities', function ($activities) {
                return $activities->total() === 1
                    && $activities->first()->action === 'admin.created';
            });
    }
}
