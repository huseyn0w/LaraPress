<?php

namespace App\Mcp\Tools\Tags;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\TagRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a post tag in the given locale. Requires the manage_posts permission.')]
class CreateTagTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected TagRepository $tags) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('Tag name.')->required(),
            'locale' => $schema->string()->description('Language code, e.g. "en". Defaults to the site default.'),
            'slug' => $schema->string()->description('URL slug. Auto-generated from the name when omitted.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);

        $name = $validated['name'];
        $slug = $validated['slug'] ?? Str::slug($name);

        $tag = $this->tags->findOrCreateByName($name);

        return Response::structured([
            'created' => true,
            'id' => $tag->id,
            'slug' => $slug,
        ]);
    }
}
