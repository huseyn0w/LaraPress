<?php

namespace Tests\Feature\Tags;

use App\Http\Models\Post;
use App\Http\Models\Tag;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Parity FEATURE_MATRIX §2 (Tags): a translatable Tag with a per-locale
 * name/slug, M2M with posts.
 */
class TagModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        app()->setLocale('en');
    }

    public function test_a_tag_stores_a_translatable_name_and_slug(): void
    {
        $tag = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $this->assertDatabaseHas('tag_translations', [
            'tag_id' => $tag->id,
            'locale' => 'en',
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);
        $this->assertSame('Laravel', $tag->fresh()->name);
        $this->assertSame('laravel', $tag->fresh()->slug);
    }

    public function test_a_tag_is_many_to_many_with_posts(): void
    {
        $tag = Tag::create(['name' => 'PHP', 'slug' => 'php']);
        $post = Post::findOrFail(1);

        $post->tags()->attach($tag->id);

        $this->assertTrue($post->tags()->where('tags.id', $tag->id)->exists());
        $this->assertTrue($tag->posts()->where('posts.id', $post->id)->exists());
    }
}
