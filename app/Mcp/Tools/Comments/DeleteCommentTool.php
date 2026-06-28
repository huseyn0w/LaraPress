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

#[Description('Destructive. Delete a comment by id. Requires the manage_comments permission. Confirm the id with list/get first.')]
class DeleteCommentTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelCommentRepository $comments) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The comment id to delete.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_comments')) {
            return $denied;
        }

        $validated = $request->validate(['id' => ['required', 'integer']]);

        $ok = $this->comments->delete($validated['id']);

        return $ok
            ? Response::structured(['deleted' => true, 'id' => $validated['id']])
            : Response::error("Could not delete comment {$validated['id']} (it may not exist).");
    }
}
