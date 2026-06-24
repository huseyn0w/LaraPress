<?php

namespace App\Services\Front;

use App\Events\CommentSubmitted;
use App\Repositories\PostCommentsRepository;
use App\Services\BaseCrudService;

/**
 * Front-end comment service: create/update/delete of post comments, delegating
 * to PostCommentsRepository (which derives ownership/approval server-side). The
 * controller maps the returned domain results to HTTP responses.
 */
class CommentService extends BaseCrudService
{
    public function __construct(private PostCommentsRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Persist a new comment and, on success, emit CommentSubmitted so a queued
     * listener notifies the post author/moderators. The side effect is never
     * fired inline here (see REFACTOR_PLAN.md §1c).
     */
    public function create($request)
    {
        $comment = $this->repo->create($request);

        if ($comment) {
            CommentSubmitted::dispatch($comment);
        }

        return $comment;
    }

    /**
     * Update a comment from the submitted request (the repository derives the
     * comment id + authorises the owner/admin). Overrides the id-based base
     * signature because comment edits are request-driven. Returns a bool.
     */
    public function update($request, $id = null)
    {
        return $this->repo->update($request, $id);
    }
}
