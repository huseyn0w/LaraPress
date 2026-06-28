<?php

namespace App\Mcp\Tools\Tags;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\TagRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Destructive. Delete a tag by id. Requires the manage_posts permission. Confirm the id with list/get first.')]
class DeleteTagTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected TagRepository $tags) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The tag id to delete.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate(['id' => ['required', 'integer']]);

        $ok = $this->tags->delete($validated['id']);

        return $ok
            ? Response::structured(['deleted' => true, 'id' => $validated['id']])
            : Response::error("Could not delete tag {$validated['id']} (it may not exist).");
    }
}
