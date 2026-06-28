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

#[Description('Read-only. List post comments, paginated, newest first. Use to discover comment ids for moderation.')]
class ListCommentsTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelCommentRepository $comments) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'per_page' => $schema->integer()->description('Comments per page (1-100). Defaults to 50.'),
            'page' => $schema->integer()->description('1-based page number. Defaults to 1.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_comments')) {
            return $denied;
        }

        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $paginator = $this->comments->paginate($validated['per_page'] ?? 50, $validated['page'] ?? 1);

        return Response::structured([
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'comments' => collect($paginator->items())->map(fn ($c) => [
                'id' => $c->id,
                'post_id' => $c->post_id,
                'user_id' => $c->user_id,
                'parent_id' => $c->parent_id,
                'comment' => $c->comment,
                'status' => $c->status,
            ])->all(),
        ]);
    }
}
