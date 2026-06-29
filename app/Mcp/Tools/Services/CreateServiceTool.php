<?php

namespace App\Mcp\Tools\Services;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelServiceRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a new service in the given locale. Provide a title; slug is derived from the title when omitted. Content/excerpt HTML is sanitised server-side. Requires the manage_services permission.')]
class CreateServiceTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected CPanelServiceRepository $services) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->description('Service title.')->required(),
            'locale' => $schema->string()->description('Language code, e.g. "en". Defaults to the site default.'),
            'slug' => $schema->string()->description('URL slug. Auto-generated from the title when omitted.'),
            'excerpt' => $schema->string()->description('Short summary shown in the services grid.'),
            'content' => $schema->string()->description('Service body (HTML allowed; it is sanitised server-side).'),
            'icon' => $schema->string()->description('Optional icon name or emoji shown in the grid.'),
            'thumbnail' => $schema->string()->description('Optional thumbnail image URL.'),
            'sort_order' => $schema->integer()->description('Grid ordering (lower comes first). Defaults to 0.'),
            'status' => $schema->integer()->description('Publication status: 1 = published, 0 = private. Defaults to 0.'),
            'meta_keywords' => $schema->string()->description('SEO meta keywords.'),
            'meta_description' => $schema->string()->description('SEO meta description.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_services')) {
            return $denied;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'locale' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:160'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'integer', 'in:0,1'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);
        unset($validated['locale']);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['status'] = $validated['status'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $service = $this->services->create($validated);

        return Response::structured([
            'created' => true,
            'id' => $service->id ?? null,
            'slug' => $validated['slug'],
        ]);
    }
}
