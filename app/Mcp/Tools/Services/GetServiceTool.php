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

#[Description('Read-only. Fetch a single service by id, including its translated fields (title, content, excerpt, status, meta) for the requested locale. Requires the manage_services permission.')]
class GetServiceTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected CPanelServiceRepository $services) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The service id.')
                ->required(),
            'locale' => $schema->string()
                ->description('Language code for the translation to read, e.g. "en". Defaults to the site default.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_services')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'locale' => ['nullable', 'string', 'max:10'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);

        $service = $this->services->getBy('id', $validated['id']);

        if (is_null($service)) {
            return Response::error("No service found with id {$validated['id']}.");
        }

        return Response::structured([
            'id' => $service->id,
            'slug' => $service->slug ?? null,
            'title' => $service->title ?? null,
            'icon' => $service->icon ?? null,
            'excerpt' => $service->excerpt ?? null,
            'content' => $service->content ?? null,
            'thumbnail' => $service->thumbnail ?? null,
            'sort_order' => $service->sort_order ?? null,
            'status' => $service->status ?? null,
            'meta_keywords' => $service->meta_keywords ?? null,
            'meta_description' => $service->meta_description ?? null,
        ]);
    }
}
