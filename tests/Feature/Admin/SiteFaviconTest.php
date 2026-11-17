<?php

namespace Tests\Feature\Admin;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteFaviconTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(array $permissions = ['settings.manage']): User
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'admin',
            'is_super_admin' => false,
            'admin_level' => 70,
            'admin_permissions' => $permissions,
            'is_active' => true,
        ])->save();

        return $user->fresh();
    }

    private function validData(array $overrides = []): array
    {
        return array_replace([
            'brand_name' => 'DAKEN',
            'contact_email' => 'info@example.com',
            'contact_phone' => '',
            'instagram_url' => '',
            'facebook_url' => '',
            'tiktok_url' => '',
        ], $overrides);
    }

    private function faviconPath(): ?string
    {
        return app(SiteSettings::class)->get('favicon');
    }
    private function validFavicon(string $name = 'favicon.png'): UploadedFile
    {
        return new UploadedFile(
            public_path('favicon-default.png'),
            $name,
            'image/png',
            null,
            true
        );
    }
    public function test_authorized_admin_can_upload_favicon(): void
    {
        Storage::fake('public');

        $this->actingAs($this->makeAdmin())
            ->put(route('admin.settings.update'), $this->validData([
                'favicon' => $this->validFavicon('favicon.png'),
            ]))
            ->assertRedirect(route('admin.settings.edit'));

        $path = $this->faviconPath();

        $this->assertNotNull($path);
        $this->assertStringStartsWith('branding/favicons/', $path);

        Storage::disk('public')->assertExists($path);
    }

    public function test_replacing_favicon_removes_previous_file(): void
    {
        Storage::fake('public');

        $oldPath = 'branding/favicons/old.png';

        Storage::disk('public')->put($oldPath, 'old image');

        app(SiteSettings::class)->setMany([
            'favicon' => $oldPath,
        ]);

        $this->actingAs($this->makeAdmin())
            ->put(route('admin.settings.update'), $this->validData([
                'favicon' => $this->validFavicon('new.png'),
            ]))
            ->assertRedirect(route('admin.settings.edit'));

        $newPath = $this->faviconPath();

        $this->assertNotNull($newPath);
        $this->assertNotSame($oldPath, $newPath);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_admin_can_restore_default_favicon(): void
    {
        Storage::fake('public');

        $oldPath = 'branding/favicons/old.png';

        Storage::disk('public')->put($oldPath, 'old image');

        app(SiteSettings::class)->setMany([
            'favicon' => $oldPath,
        ]);

        $this->actingAs($this->makeAdmin())
            ->put(route('admin.settings.update'), $this->validData([
                'reset_favicon' => '1',
            ]))
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertNull($this->faviconPath());

        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_invalid_favicon_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->makeAdmin())
            ->put(route('admin.settings.update'), $this->validData([
                'favicon' => UploadedFile::fake()->create(
                    'invalid.txt',
                    10,
                    'text/plain'
                ),
            ]))
            ->assertSessionHasErrors('favicon');

        $this->assertNull($this->faviconPath());
    }

    public function test_upload_and_reset_cannot_be_requested_together(): void
    {
        Storage::fake('public');

        $this->actingAs($this->makeAdmin())
            ->put(route('admin.settings.update'), $this->validData([
                'favicon' => $this->validFavicon('favicon.png'),
                'reset_favicon' => '1',
            ]))
            ->assertSessionHasErrors('favicon');

        $this->assertNull($this->faviconPath());
    }

    public function test_admin_without_permission_cannot_change_favicon(): void
    {
        Storage::fake('public');

        $this->actingAs($this->makeAdmin(['products.manage']))
            ->put(route('admin.settings.update'), $this->validData([
                'favicon' => $this->validFavicon('favicon.png'),
            ]))
            ->assertForbidden();

        $this->assertNull($this->faviconPath());
    }

    public function test_public_page_uses_default_favicon(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('favicon-default.png?v=default', false);
    }

    public function test_public_page_uses_uploaded_favicon(): void
    {
        $path = 'branding/favicons/custom.png';

        app(SiteSettings::class)->setMany([
            'favicon' => $path,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee(
                'storage/' . $path . '?v=' . md5($path),
                false
            );
    }


    public function test_favicon_version_changes_when_path_changes(): void
    {
        $settings = app(SiteSettings::class);

        $firstPath = 'branding/favicons/first.png';
        $secondPath = 'branding/favicons/second.png';

        $settings->setMany(['favicon' => $firstPath]);

        $this->get('/')
            ->assertOk()
            ->assertSee('?v=' . md5($firstPath), false);

        $settings->setMany(['favicon' => $secondPath]);

        $this->get('/')
            ->assertOk()
            ->assertSee('?v=' . md5($secondPath), false)
            ->assertDontSee('?v=' . md5($firstPath), false);
    }

    public function test_admin_settings_page_displays_favicon_control(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('name="favicon"', false)
            ->assertSee('name="reset_favicon"', false);
    }
}
