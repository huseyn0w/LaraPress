<?php

namespace Tests\Unit;

use App\Http\Models\Category;
use App\Http\Models\CategoryTranslation;
use App\Repositories\CategoryRepository;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Direct unit/integration tests for the front-facing CategoryRepository.
 *
 * The repository is DB-backed (translatable join query), so we use RefreshDatabase
 * and seed the standard fixtures. Mirrors RepositoryBehaviorTest style: resolve
 * via the container, assert representative read results.
 */
class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** sitemapEntries returns at least one row per seeded category translation. */
    public function test_sitemap_entries_returns_seeded_category_translations(): void
    {
        $repo = app(CategoryRepository::class);

        $entries = $repo->sitemapEntries();

        $this->assertNotEmpty($entries);
        // Each entry must carry the slug used to build the <url> element.
        $this->assertTrue($entries->every(fn ($e) => ! empty($e->slug)));
    }

    /** llmsEntries returns category rows for the default app locale. */
    public function test_llms_entries_returns_rows_for_default_locale(): void
    {
        $repo = app(CategoryRepository::class);

        $entries = $repo->llmsEntries();

        $this->assertNotEmpty($entries);
        $this->assertTrue($entries->every(fn ($e) => ! empty($e->title) && ! empty($e->slug)));
    }

    /** displayList returns a paginator of posts for the seeded category. */
    public function test_display_list_returns_paginator_for_category(): void
    {
        $repo = app(CategoryRepository::class);

        // Category id 1 is seeded and has at least one post attached.
        $result = $repo->displayList(1);

        $this->assertNotNull($result);
        // Verify the paginator contract.
        $this->assertTrue(method_exists($result, 'total'), 'displayList must return a paginator.');
    }

    /** Direct instantiation without the container also works (constructor takes a model). */
    public function test_direct_instantiation_resolves_category_model(): void
    {
        $repo = new CategoryRepository(new Category);

        // sitemapEntries should still function when manually constructed.
        $entries = $repo->sitemapEntries();
        $this->assertNotEmpty($entries);
    }

    /** sitemapEntries includes the seeded category slug for the default locale. */
    public function test_sitemap_entries_includes_seeded_slug(): void
    {
        // Confirm a seeded category translation exists for 'en'.
        $seededSlug = CategoryTranslation::where('locale', 'en')->value('slug');
        $this->assertNotNull($seededSlug, 'A seeded category translation for en must exist.');

        $repo = app(CategoryRepository::class);
        $slugs = $repo->sitemapEntries()->pluck('slug');

        $this->assertTrue($slugs->contains($seededSlug));
    }
}
