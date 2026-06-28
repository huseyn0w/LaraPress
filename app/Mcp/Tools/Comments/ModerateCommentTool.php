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

#[Description('Approve or un-approve a comment by id. Requires the manage_comments permission.')]
class ModerateCommentTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelCommentRepository $comments) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The comment id to moderate.')->required(),
            'approved' => $schema->boolean()->description('Pass true to approve, false to un-approve.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_comments')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'approved' => ['required', 'boolean'],
        ]);

        $id = $validated['id'];
        $ok = $validated['approved']
            ? $this->comments->approve($id)
            : $this->comments->unApprove($id);

        return $ok
            ? Response::structured([
                'moderated' => true,
                'id' => $id,
                'status' => $validated['approved'] ? 'approved' : 'unapproved',
            ])
            : Response::error("Could not moderate comment {$id} (it may not exist).");
    }
}
