<?php

namespace Tests\Feature;

use App\Support\Hooks;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class HookDirectiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // The global '*' view composer reads page data, so a rendered view needs
        // the seeded fixtures present.
        $this->seed(DatabaseSeeder::class);
    }

    public function test_hooks_is_a_shared_singleton(): void
    {
        $this->assertInstanceOf(Hooks::class, app('hooks'));
        $this->assertSame(app('hooks'), app('hooks'));
        $this->assertSame(app('hooks'), app(Hooks::class));
    }

    public function test_hook_directive_renders_region_output(): void
    {
        app('hooks')->onRegion('footer', fn () => '<span id="z">hi</span>');

        $rendered = Blade::render("@hook('footer')");

        $this->assertStringContainsString('<span id="z">hi</span>', $rendered);
    }
}
