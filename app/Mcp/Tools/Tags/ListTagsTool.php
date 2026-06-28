<?php

namespace App\Mcp\Tools\Tags;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\TagRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. List post tags for a given locale, paginated. Use to discover tag ids/slugs.')]
class ListTagsTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected TagRepository $tags) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'locale' => $schema->string()->description('Language code, e.g. "en". Defaults to the site default.'),
            'per_page' => $schema->integer()->description('Tags per page (1-100). Defaults to 50.'),
            'page' => $schema->integer()->description('1-based page number. Defaults to 1.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate([
            'locale' => ['nullable', 'string', 'max:10'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);

        $paginator = $this->tags->only($validated['per_page'] ?? 50, $validated['page'] ?? 1);

        return Response::structured([
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'tags' => collect($paginator->items())->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name ?? null,
                'slug' => $t->slug ?? null,
            ])->all(),
        ]);
    }
}
