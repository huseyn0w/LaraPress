<?php

namespace Tests\Feature\Revisions;

use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Models\PostTranslation;
use App\Http\Models\Revision;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Revisions are immutable snapshots of a post translation taken *before* each
 * admin update. Creating a post takes no snapshot (nothing to preserve); the
 * first edit snapshots the pre-edit state, and so on.
 */
class PostRevisionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('username', 'admin')->firstOrFail();
    }

    private function postPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Round Trip Post',
            'slug' => 'round-trip-post',
            'content' => 'original body',
            'preview' => 'original preview',
            'author_id' => $this->admin->id,
            'meta_keywords' => 'kw',
            'meta_description' => 'md',
            'category' => [1],
            'status' => 1,
        ], $overrides);
    }

    public function test_creating_a_post_takes_no_revision(): void
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());

        $this->assertSame(0, Revision::count(), 'Creating a post must not snapshot.');
    }

    public function test_updating_a_post_snapshots_the_previous_translation(): void
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());
        $translation = PostTranslation::where('slug', 'round-trip-post')->firstOrFail();

        $this->actingAs($this->admin)->put(
            '/cmstack-laravel-admin/posts/'.$translation->post_id.'/update',
            $this->postPayload(['content' => 'edited body'])
        );

        $this->assertSame(1, Revision::count(), 'One revision after the first edit.');

        $revision = Revision::firstOrFail();
        $this->assertSame(PostTranslation::class, $revision->revisionable_type);
        $this->assertSame($translation->id, $revision->revisionable_id);
        $this->assertSame($this->admin->id, $revision->user_id);
        // The snapshot preserves the PRE-edit content, not the new value.
        $this->assertStringContainsString('original body', $revision->data['content']);
        $this->assertSame('Round Trip Post', $revision->data['title']);
    }

    public function test_each_edit_appends_a_new_revision(): void
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());
        $translation = PostTranslation::where('slug', 'round-trip-post')->firstOrFail();

        $this->actingAs($this->admin)->put('/cmstack-laravel-admin/posts/'.$translation->post_id.'/update', $this->postPayload(['content' => 'edit one']));
        $this->actingAs($this->admin)->put('/cmstack-laravel-admin/posts/'.$translation->post_id.'/update', $this->postPayload(['content' => 'edit two']));

        $this->assertSame(2, Revision::count(), 'Each edit appends one revision.');
    }

    public function test_restoring_a_revision_reverts_content_and_snapshots_current(): void
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());
        $translation = PostTranslation::where('slug', 'round-trip-post')->firstOrFail();

        // Edit: snapshots the 'original body' state as the first revision.
        $this->actingAs($this->admin)->put(
            '/cmstack-laravel-admin/posts/'.$translation->post_id.'/update',
            $this->postPayload(['content' => 'edited body'])
        );
        $revision = Revision::firstOrFail();
        $this->assertStringContainsString('original body', $revision->data['content']);

        // Restore the original revision.
        $this->actingAs($this->admin)->post(
            '/cmstack-laravel-admin/posts/'.$translation->post_id.'/revisions/'.$revision->id.'/restore/en'
        )->assertRedirect();

        // Content reverted to the snapshot.
        $fresh = PostTranslation::findOrFail($translation->id);
        $this->assertStringContainsString('original body', $fresh->content);

        // Restore is itself an edit: the pre-restore ('edited body') state is
        // captured as a new revision, so the restore is undoable.
        $this->assertSame(2, Revision::count(), 'Restore snapshots the pre-restore state.');
        $this->assertStringContainsString('edited body', Revision::orderByDesc('id')->firstOrFail()->data['content']);
    }

    public function test_revisions_list_page_renders(): void
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());
        $translation = PostTranslation::where('slug', 'round-trip-post')->firstOrFail();
        $this->actingAs($this->admin)->put('/cmstack-laravel-admin/posts/'.$translation->post_id.'/update', $this->postPayload(['content' => 'edited body']));

        $this->actingAs($this->admin)
            ->get('/cmstack-laravel-admin/posts/'.$translation->post_id.'/revisions/en')
            ->assertOk()
            ->assertSee('v1')
            ->assertSee($this->admin->username);
    }

    public function test_revision_diff_page_renders_and_marks_changes(): void
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());
        $translation = PostTranslation::where('slug', 'round-trip-post')->firstOrFail();
        $this->actingAs($this->admin)->put('/cmstack-laravel-admin/posts/'.$translation->post_id.'/update', $this->postPayload(['content' => 'edited body']));
        $revision = Revision::firstOrFail();

        $this->actingAs($this->admin)
            ->get('/cmstack-laravel-admin/posts/'.$translation->post_id.'/revisions/'.$revision->id.'/compare/en')
            ->assertOk()
            ->assertSee('original body')
            ->assertSee('edited body');
    }

    public function test_cannot_restore_a_revision_belonging_to_another_post(): void
    {
        // Post A with one revision.
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());
        $a = PostTranslation::where('slug', 'round-trip-post')->firstOrFail();
        $this->actingAs($this->admin)->put('/cmstack-laravel-admin/posts/'.$a->post_id.'/update', $this->postPayload(['content' => 'a edited']));
        $revisionOfA = Revision::firstOrFail();

        // Post B with distinct content so a clobber is detectable.
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload([
            'title' => 'Second Post', 'slug' => 'second-post', 'content' => 'b untouched body',
        ]));
        $b = PostTranslation::where('slug', 'second-post')->firstOrFail();

        // Try to restore A's revision under B's id -> must 404 and not touch B.
        $this->actingAs($this->admin)->post(
            '/cmstack-laravel-admin/posts/'.$b->post_id.'/revisions/'.$revisionOfA->id.'/restore/en'
        )->assertNotFound();

        $freshB = PostTranslation::findOrFail($b->id);
        $this->assertStringContainsString('b untouched body', $freshB->content, 'B must be untouched.');
    }
}
