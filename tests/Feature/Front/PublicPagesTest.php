<?php

namespace Tests\Feature\Front;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Public front-end rendering: home, pages, posts, category archives, search,
 * public user profiles and the localized route variants. All read-only and
 * available to guests. Seeded fixtures (see CPanel*Seeder) provide:
 *   page  "/" (home), page "contact"
 *   post  "post-example"
 *   category "parent_category" / "about"
 *   user  "admin"
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_home_page_renders(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_content_page_renders(): void
    {
        $this->get('/contact')->assertStatus(200);
    }

    public function test_single_post_renders(): void
    {
        $this->get('/posts/post-example')->assertStatus(200);
    }

    public function test_missing_post_returns_404(): void
    {
        $this->get('/posts/this-post-does-not-exist')->assertStatus(404);
    }

    public function test_category_archive_renders(): void
    {
        $this->get('/category/parent_category')->assertStatus(200);
    }

    public function test_category_paginated_page_renders(): void
    {
        $this->get('/category/parent_category/page/1')->assertStatus(200);
    }

    public function test_public_user_profile_renders(): void
    {
        $this->get('/users/admin')->assertStatus(200);
    }

    public function test_localized_post_route_renders(): void
    {
        // {locale?}/posts/{slug}; en is the default locale.
        $this->get('/en/posts/post-example')->assertStatus(200);
    }

    public function test_localized_category_route_renders(): void
    {
        $this->get('/en/category/parent_category')->assertStatus(200);
    }
}
