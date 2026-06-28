<?php

namespace App\Mcp\Tools\Tags;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\HydratesRequest;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\TagRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update a tag by id for a given locale. Only the fields you pass are changed. Requires the manage_posts permission.')]
class UpdateTagTool extends Tool
{
    use AuthorizesAccess;
    use HydratesRequest;
    use ResolvesLocale;

    public function __construct(protected TagRepository $tags) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The tag id to update.')->required(),
            'locale' => $schema->string()->description('Language code of the translation to update, e.g. "en".'),
            'name' => $schema->string()->description('New tag name.'),
            'slug' => $schema->string()->description('New URL slug.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'locale' => ['nullable', 'string', 'max:10'],
            'name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        $id = $validated['id'];
        unset($validated['id'], $validated['locale']);

        $this->applyLocale($request->get('locale'));

        if (empty($validated)) {
            return Response::error('Nothing to update: provide at least one field besides id and locale.');
        }

        $this->hydrateRequest($validated);

        $ok = $this->tags->update($id, $validated);

        return $ok
            ? Response::structured(['updated' => true, 'id' => $id, 'fields' => array_keys($validated)])
            : Response::error("Could not update tag {$id} (it may not exist).");
    }
}
