<?php

namespace Tests\Feature;

use App\Events\CommentSubmitted;
use App\Http\Models\Post;
use App\Http\Models\User;
use App\Mail\CommentSubmittedMail;
use App\Services\Front\CommentService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Parity §18: notify the post author/moderators on a new comment. The write
 * path emits a CommentSubmitted domain event; a queued listener mails the
 * recipients (no inline side effect in the service).
 */
class CommentNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function commentRequest(): Request
    {
        return new Request([
            'post_id' => 1,
            'parent_id' => null,
            'comment' => 'Great write-up, thanks!',
        ]);
    }

    public function test_creating_a_comment_dispatches_the_comment_submitted_event(): void
    {
        Event::fake([CommentSubmitted::class]);

        $commenter = User::factory()->create(['role_id' => 2]);
        $this->actingAs($commenter);

        app(CommentService::class)->create($this->commentRequest());

        Event::assertDispatched(CommentSubmitted::class);
    }

    public function test_a_failed_comment_create_dispatches_no_event(): void
    {
        Event::fake([CommentSubmitted::class]);

        // Not logged in -> the repository refuses the write and returns false.
        app(CommentService::class)->create($this->commentRequest());

        Event::assertNotDispatched(CommentSubmitted::class);
    }

    public function test_new_comment_emails_the_post_author(): void
    {
        Mail::fake();

        $author = User::factory()->create(['role_id' => 2, 'email' => 'author@example.com']);
        $post = Post::findOrFail(1);
        $post->author_id = $author->id;
        $post->save();

        $commenter = User::factory()->create(['role_id' => 2, 'email' => 'commenter@example.com']);
        $this->actingAs($commenter);

        app(CommentService::class)->create($this->commentRequest());

        Mail::assertSent(CommentSubmittedMail::class, function (CommentSubmittedMail $mail) use ($author) {
            return $mail->hasTo($author->email);
        });
    }
}
