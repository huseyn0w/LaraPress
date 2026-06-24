<?php

namespace App\Events;

use App\Http\Models\Comments;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Emitted by the comment write path when a new comment is persisted.
 *
 * Classification: ASYNCHRONOUS — the notification listener is queued
 * (fire-and-forget email; it must not block or roll back the comment write).
 * See REFACTOR_PLAN.md §1c.
 */
class CommentSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public Comments $comment) {}
}
