<?php

namespace App\Mcp\Tools\Posts;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Services\CPanel\CPanelPostService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. List the revision history for a post translation (newest first, paginated). Each revision carries its id, created_at timestamp, and the editor\'s id. Requires the manage_posts permission.')]
class ListPostRevisionsTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelPostService $posts) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The post id.')
                ->required(),
            'locale' => $schema->string()
                ->description('Language code of the translation whose revision history to list, e.g. "en". Defaults to the site default.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'locale' => ['required', 'string', 'max:10'],
        ]);

        $result = $this->posts->revisionsFor((int) $validated['id'], $validated['locale']);

        if (is_null($result['current'])) {
            return Response::error("No '{$validated['locale']}' translation found for post {$validated['id']}.");
        }

        $revisions = $result['revisions'];

        return Response::structured([
            'post_id' => $validated['id'],
            'locale' => $validated['locale'],
            'total' => $revisions->total(),
            'current_page' => $revisions->currentPage(),
            'revisions' => collect($revisions->items())->map(fn ($rev) => [
                'id' => $rev->id,
                'created_at' => $rev->created_at?->toIso8601String(),
                'user_id' => $rev->user_id,
            ])->all(),
        ]);
    }
}
