<?php

namespace App\Mcp\Tools\Services;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelServiceRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. List services for a given locale, paginated. Returns id, slug, status, sort order and timestamps. Use this to discover service ids/slugs before getting, updating or deleting a service. Requires the manage_services permission.')]
class ListServicesTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected CPanelServiceRepository $services) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'locale' => $schema->string()
                ->description('Language code to list services for, e.g. "en". Defaults to the site default.'),
            'per_page' => $schema->integer()
                ->description('How many services per page (1-100). Defaults to 20.'),
            'page' => $schema->integer()
                ->description('1-based page number. Defaults to 1.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_services')) {
            return $denied;
        }

        $validated = $request->validate([
            'locale' => ['nullable', 'string', 'max:10'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);

        $perPage = $validated['per_page'] ?? 20;
        $page = $validated['page'] ?? 1;

        $paginator = $this->services->only($perPage, $page);

        return Response::structured([
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'services' => collect($paginator->items())->map(fn ($s) => [
                'id' => $s->id,
                'slug' => $s->slug ?? null,
                'title' => $s->title ?? null,
                'status' => $s->status ?? null,
                'sort_order' => $s->sort_order ?? null,
                'updated_at' => (string) ($s->updated_at ?? ''),
            ])->all(),
        ]);
    }
}
