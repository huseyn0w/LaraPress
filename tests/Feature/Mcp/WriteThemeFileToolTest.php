<?php

namespace Tests\Feature\Mcp;

use App\Http\Models\User;
use App\Http\Models\UserRoles;
use App\Mcp\Servers\CmstackLaravelServer;
use App\Mcp\Tools\Theme\WriteThemeFileTool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Security-critical tests for WriteThemeFileTool.
 *
 * The tool must:
 *  - reject path-traversal attempts WITHOUT writing anything to disk,
 *  - reject non-.blade.php extensions,
 *  - deny callers who lack manage_general_settings,
 *  - write successfully for a valid .blade.php path when permitted.
 *
 * Auth/permission setup mirrors CmstackLaravelServerTest.
 */
class WriteThemeFileToolTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPermissions(array $flags): User
    {
        $role = UserRoles::create([
            'name' => 'role_'.bin2hex(random_bytes(4)),
            'permissions' => json_encode($flags),
        ]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    /** Path pointing outside the theme must be rejected and must NOT write a file. */
    public function test_rejects_path_traversal_and_does_not_write(): void
    {
        $user = $this->userWithPermissions(['manage_general_settings' => 1]);

        // A traversal that resolves outside the theme root.
        CmstackLaravelServer::actingAs($user)
            ->tool(WriteThemeFileTool::class, [
                'path' => '../../.env',
                'contents' => 'MALICIOUS=1',
                'create' => true,
            ])
            ->assertSee('Rejected path');

        // The .env file must NOT have been overwritten.
        $this->assertStringNotContainsString('MALICIOUS=1', file_get_contents(base_path('.env')));
    }

    /** Absolute path supplied by the caller must be rejected. */
    public function test_rejects_absolute_path(): void
    {
        $user = $this->userWithPermissions(['manage_general_settings' => 1]);

        CmstackLaravelServer::actingAs($user)
            ->tool(WriteThemeFileTool::class, [
                'path' => '/etc/passwd.blade.php',
                'contents' => 'evil',
                'create' => true,
            ])
            ->assertSee('Rejected path');
    }

    /** Paths that do not end in .blade.php must be rejected. */
    public function test_rejects_non_blade_php_extension(): void
    {
        $user = $this->userWithPermissions(['manage_general_settings' => 1]);

        CmstackLaravelServer::actingAs($user)
            ->tool(WriteThemeFileTool::class, [
                'path' => 'partials/evil.php',
                'contents' => '<?php system("rm -rf /"); ?>',
                'create' => true,
            ])
            ->assertSee('Rejected path');
    }

    /** A caller without manage_general_settings must receive a Permission denied error. */
    public function test_denies_caller_without_required_permission(): void
    {
        $user = $this->userWithPermissions(['manage_general_settings' => 0]);

        CmstackLaravelServer::actingAs($user)
            ->tool(WriteThemeFileTool::class, [
                'path' => 'index.blade.php',
                'contents' => 'hello',
            ])
            ->assertSee('Permission denied');
    }

    /** A permitted user writing a valid .blade.php path that already exists must succeed. */
    public function test_writes_successfully_for_valid_blade_path(): void
    {
        $user = $this->userWithPermissions(['manage_general_settings' => 1]);

        // Use an existing template from the default theme (index.blade.php is
        // confirmed to exist by CmstackLaravelServerTest::test_theme_listing_returns_known_template).
        $themePath = resource_path('views/'.config('app.template_name', 'default').'/index.blade.php');

        // Back up the current contents so we can restore after the test.
        $original = file_get_contents($themePath);

        try {
            CmstackLaravelServer::actingAs($user)
                ->tool(WriteThemeFileTool::class, [
                    'path' => 'index.blade.php',
                    'contents' => '{{-- written by test --}}',
                ])
                ->assertOk()
                ->assertSee('written');

            $this->assertStringContainsString('written by test', file_get_contents($themePath));
        } finally {
            // Always restore the original template.
            file_put_contents($themePath, $original);
        }
    }
}
