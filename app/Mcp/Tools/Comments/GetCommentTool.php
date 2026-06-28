<?php

namespace App\Mcp\Tools\Comments;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelCommentRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. Fetch a single comment by id with its status and body.')]
class GetCommentTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelCommentRepository $comments) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The comment id.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_comments')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $comment = $this->comments->find($validated['id']);

        if (is_null($comment)) {
            return Response::error("No comment found with id {$validated['id']}.");
        }

        return Response::structured([
            'id' => $comment->id,
            'post_id' => $comment->post_id,
            'user_id' => $comment->user_id,
            'parent_id' => $comment->parent_id,
            'comment' => $comment->comment,
            'status' => $comment->status,
        ]);
    }
}
