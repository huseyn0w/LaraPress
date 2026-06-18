<?php

namespace Tests\Browser;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\ReadsComputedStyle;
use Tests\DuskTestCase;

/**
 * e2e: the public site renders styled, internal links carry the correct host
 * (the APP_URL "missing port" bug produced http://127.0.0.1/... links), the
 * language switcher actually loads the Russian version, and a post opens.
 */
class PublicSiteTest extends DuskTestCase
{
    use DatabaseMigrations;
    use ReadsComputedStyle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_homepage_is_styled_and_post_links_carry_the_port(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSourceHas('theme-default')
                ->assertVisible('header');

            // STYLE: the sticky header must be laid out as a flex row (Tailwind
            // applied), not an unstyled stacked block.
            $display = $browser->script(
                "return getComputedStyle(document.querySelector('header .mx-auto, header > div')).display"
            )[0];
            $this->assertStringContainsString('flex', (string) $display, "Header is not styled (display: {$display})");

            // LINKS: a post link must be an absolute URL that includes the real
            // port (:8000 here). The bug emitted http://127.0.0.1/posts/... (no port).
            $href = $browser->script(
                "var a=document.querySelector('a[href*=\"/posts/\"]'); return a?a.getAttribute('href'):null"
            )[0];
            $this->assertNotNull($href, 'No post link found on the homepage');
            $this->assertStringContainsString('127.0.0.1:8000/posts/', $href, "Post link missing port: {$href}");
        });
    }

    public function test_language_switcher_loads_the_russian_version(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSourceHas('lang="en"')
                ->clickLink('Русский')
                ->waitForText('Свежие новости')
                ->assertSourceHas('lang="ru"')
                ->assertSee('Свежие новости');
        });
    }

    public function test_a_post_opens_from_the_homepage(): void
    {
        $this->browse(function (Browser $browser) {
            // Dusk reuses the browser across tests, so a prior test may have left
            // a RU locale cookie; clear it so the EN post slug resolves.
            $browser->visit('/')->driver->manage()->deleteAllCookies();

            $browser->visit('/posts/post-example')
                ->waitFor('h1', 10)
                ->assertSee('Post example');

            $themed = $browser->script("return document.body.classList.contains('theme-default')")[0];
            $this->assertTrue($themed, 'Post page is not using the styled theme-default layout');
        });
    }
}
