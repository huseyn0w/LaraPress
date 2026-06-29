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

#[Description('Update an existing service by id, for a given locale. Only the fields you pass are changed. Requires the manage_services permission.')]
class UpdateServiceTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected CPanelServiceRepository $services) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The service id to update.')->required(),
            'locale' => $schema->string()->description('Language code of the translation to update, e.g. "en".'),
            'title' => $schema->string()->description('New title.'),
            'slug' => $schema->string()->description('New URL slug.'),
            'excerpt' => $schema->string()->description('New short summary.'),
            'content' => $schema->string()->description('New body (HTML allowed; sanitised server-side).'),
            'icon' => $schema->string()->description('New icon name or emoji.'),
            'thumbnail' => $schema->string()->description('New thumbnail image URL.'),
            'sort_order' => $schema->integer()->description('New grid ordering (lower comes first).'),
            'status' => $schema->integer()->description('Publication status: 1 = published, 0 = private.'),
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
            'id' => ['required', 'integer'],
            'locale' => ['nullable', 'string', 'max:10'],
            'title' => ['nullable', 'string', 'max:120'],
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

        $id = $validated['id'];
        unset($validated['id'], $validated['locale']);

        $this->applyLocale($request->get('locale'));

        if (empty($validated)) {
            return Response::error('Nothing to update: provide at least one field besides id and locale.');
        }

        $ok = $this->services->update($id, $validated);

        return $ok
            ? Response::structured(['updated' => true, 'id' => $id, 'fields' => array_keys($validated)])
            : Response::error("Could not update service {$id} (it may not exist).");
    }
}
