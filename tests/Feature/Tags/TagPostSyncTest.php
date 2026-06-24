<?php

namespace Tests\Feature\Tags;

use App\Http\Models\Post;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * The PostObserver syncs the submitted `tags` to the post (find-or-create via
 * the repository), mirroring how categories are attached.
 */
class TagPostSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function bindRequest(array $input): void
    {
        $this->app->instance('request', Request::create('/test', 'POST', $input));
    }

    private function makePost(array $input): Post
    {
        $admin = User::where('username', 'admin')->firstOrFail();
        $this->bindRequest($input + ['content' => 'c', 'preview' => 'p', 'category' => [1]]);

        return Post::create([
            'title' => 'TagPost'.uniqid(),
            'slug' => 'tag-post-'.uniqid(),
            'content' => 'c',
            'preview' => 'p',
            'author_id' => $admin->id,
            'meta_keywords' => 'k',
            'meta_description' => 'd',
            'status' => 1,
        ]);
    }

    public function test_tags_are_synced_on_create(): void
    {
        $post = $this->makePost(['tags' => ['Laravel', 'PHP']]);

        $this->assertSame(2, $post->tags()->count());
        $this->assertEqualsCanonicalizing(['laravel', 'php'], $post->tags()->get()->pluck('slug')->all());
    }

    public function test_absent_tags_field_does_not_touch_tags(): void
    {
        // No `tags` key in the request at all -> tags untouched (no wipe).
        $post = $this->makePost([]);

        $this->assertSame(0, $post->tags()->count());
        // sanity: the post itself was created fine
        $this->assertDatabaseHas('post_translations', ['post_id' => $post->id]);
    }
}
