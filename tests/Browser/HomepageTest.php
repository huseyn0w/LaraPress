<?php

/**
 * Pest 4 browser tests — public site (homepage, language switcher, post page).
 *
 * Replaces the scenarios from tests/Browser/PublicSiteTest.php (Laravel Dusk).
 * These tests run ONLY when BROWSER_TESTS=1 is set (CI e2e job).
 * The Playwright Chromium binary is installed in CI via `npx playwright install chromium`.
 *
 * API used: pest-plugin-browser v4.3.1
 *   visit(url)                  → PendingAwaitablePage (lazy — connection deferred)
 *   ->assertNoSmoke()           → assertNoConsoleLogs() + assertNoJavaScriptErrors()
 *   ->assertNoAccessibilityIssues(1) → WCAG 2.1 AA (level 1 = serious+critical)
 *   ->assertSee(text)           → visible text assertion
 *   ->assertSourceHas(string)   → raw HTML source assertion
 *   ->click(selector)           → clicks element by CSS selector / text
 *   ->assertPathContains(path)  → URL path assertion
 *   ->on()->mobile()            → set Device::MOBILE viewport
 *   ->resize(w, h)              → direct viewport resize (InteractsWithViewPort)
 */
$browserEnv = (bool) env('BROWSER_TESTS', false);

it('homepage renders a styled theme-default layout with visible header', function () {
    $page = visit('/');

    $page->assertSourceHas('theme-default')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues(1);
})->skip(! $browserEnv, 'browser env (served app + Playwright Chromium) required — set BROWSER_TESTS=1');

it('homepage post links are present and navigable', function () {
    $page = visit('/');

    $page->assertPresent('[data-testid="post-link"]')
        ->click('[data-testid="post-link"]')
        ->assertPathBeginsWith('/posts/')
        ->assertNoSmoke();
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');

it('language switcher loads the Russian version of the site', function () {
    $page = visit('/');

    $page->assertSourceHas('lang="en"')
        ->click('[data-testid="lang-ru"]')
        ->assertSee('Свежие новости')
        ->assertSourceHas('lang="ru"')
        ->assertNoSmoke();
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');

it('a post page opens from a direct URL and uses the styled theme', function () {
    $page = visit('/posts/post-example');

    $page->assertSee('Post example')
        ->assertSourceHas('theme-default')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues(1);
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');

it('homepage is usable on a mobile viewport', function () {
    $page = visit('/')->on()->mobile();

    $page->assertSourceHas('theme-default')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues(1);
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');
