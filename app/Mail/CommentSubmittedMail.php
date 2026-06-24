<?php

namespace App\Mail;

use App\Http\Models\Comments;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Notifies the post author / moderators that a new comment was submitted.
 */
class CommentSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Comments $comment) {}

    public function build()
    {
        $post = $this->comment->post;
        $authorName = optional($this->comment->user)->username ?? 'A visitor';

        return $this->subject('New comment awaiting moderation')
            ->markdown('emails.comment-notification', [
                'commentBody' => $this->comment->comment,
                'authorName' => $authorName,
                'postTitle' => optional($post)->title,
                'moderateUrl' => route('cpanel_comments_list'),
            ]);
    }
}
