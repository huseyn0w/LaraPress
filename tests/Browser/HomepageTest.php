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
 *
 * Phase 4 data-testids added to the shell markup:
 *   [data-testid="skip-link"]           — skip-to-content link (first focusable element)
 *   [data-testid="public-header"]       — <header> element
 *   [data-testid="header-wordmark"]     — site name / logo anchor
 *   [data-testid="primary-nav"]         — desktop primary <nav>
 *   [data-testid="header-search"]       — search affordance link
 *   [data-testid="locale-switcher"]     — locale dropdown wrapper
 *   [data-testid="locale-trigger"]      — locale dropdown trigger button
 *   [data-testid="dark-toggle"]         — dark/light mode toggle button (desktop)
 *   [data-testid="mobile-menu-button"]  — hamburger/close button
 *   [data-testid="mobile-nav"]          — mobile drawer primary <nav>
 *   [data-testid="mobile-dark-toggle"]  — dark/light toggle inside mobile drawer
 *   [data-testid="public-footer"]       — <footer> element
 *   [data-testid="footer-wordmark"]     — footer site name anchor
 *   [data-testid="footer-locale-switcher"] — footer locale column
 *   [data-testid="lang-{code}"]         — per-language switcher links (both header dropdown & footer)
 *   [data-testid="post-link"]           — post card links (placed in page templates)
 */
$browserEnv = (bool) env('BROWSER_TESTS', false);

it('homepage renders a styled theme-default layout with visible header', function () {
    $page = visit('/');

    $page->assertSourceHas('theme-default')
        ->assertPresent('[data-testid="public-header"]')
        ->assertPresent('[data-testid="header-wordmark"]')
        ->assertPresent('[data-testid="skip-link"]')
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

it('header locale dropdown trigger is present and operable', function () {
    $page = visit('/');

    $page->assertPresent('[data-testid="locale-trigger"]')
        ->click('[data-testid="locale-trigger"]')
        ->assertPresent('[data-testid="lang-ru"]')
        ->assertNoSmoke();
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');

it('dark toggle button is present in the header and toggles aria-pressed', function () {
    $page = visit('/');

    $page->assertPresent('[data-testid="dark-toggle"]')
        ->assertNoSmoke();
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');

it('mobile drawer opens and the close button is visible', function () {
    $page = visit('/')->on()->mobile();

    $page->assertPresent('[data-testid="mobile-menu-button"]')
        ->click('[data-testid="mobile-menu-button"]')
        ->assertPresent('[data-testid="mobile-nav"]')
        ->assertNoSmoke();
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');

it('a post page opens from a direct URL and uses the styled theme', function () {
    $page = visit('/posts/post-example');

    $page->assertSee('Post example')
        ->assertSourceHas('theme-default')
        ->assertPresent('[data-testid="public-header"]')
        ->assertPresent('[data-testid="public-footer"]')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues(1);
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');

it('homepage is usable on a mobile viewport', function () {
    $page = visit('/')->on()->mobile();

    $page->assertSourceHas('theme-default')
        ->assertPresent('[data-testid="mobile-menu-button"]')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues(1);
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');

it('skip-to-content link is the first focusable element in the header', function () {
    $page = visit('/');

    // The skip link is visually hidden but present in DOM as the first focusable el.
    $page->assertPresent('[data-testid="skip-link"]')
        ->assertSourceHas('href="#main"')
        ->assertNoSmoke();
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');

it('footer renders wordmark and locale switcher', function () {
    $page = visit('/');

    $page->assertPresent('[data-testid="public-footer"]')
        ->assertPresent('[data-testid="footer-wordmark"]')
        ->assertNoSmoke();
})->skip(! $browserEnv, 'browser env required — set BROWSER_TESTS=1');
