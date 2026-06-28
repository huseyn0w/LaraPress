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

#[Description('Restore a specific revision of a post translation. The current state is snapshot before the restore, so the operation is undoable. Returns the fields that were written back. Requires the manage_posts permission.')]
class RestorePostRevisionTool extends Tool
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
                ->description('Language code of the translation to restore, e.g. "en".')
                ->required(),
            'revision_id' => $schema->integer()
                ->description('The revision id to restore (from list_post_revisions).')
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
            'revision_id' => ['required', 'integer'],
        ]);

        $ok = $this->posts->restoreRevision(
            (int) $validated['id'],
            $validated['locale'],
            (int) $validated['revision_id']
        );

        return $ok
            ? Response::structured([
                'restored' => true,
                'post_id' => $validated['id'],
                'locale' => $validated['locale'],
                'revision_id' => $validated['revision_id'],
            ])
            : Response::error(
                "Could not restore revision {$validated['revision_id']} for post {$validated['id']} locale '{$validated['locale']}'. "
                .'The post, translation, or revision may not exist, or the restore conflicted (e.g. a duplicate slug).'
            );
    }
}
