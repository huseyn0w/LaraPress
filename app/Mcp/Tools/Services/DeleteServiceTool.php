<?php

namespace App\Mcp\Tools\Services;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelServiceRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Destructive. Soft-delete a service by id (it can be restored from the admin trash). Requires the manage_services permission. Confirm the id with list/get first.')]
class DeleteServiceTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelServiceRepository $services) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The service id to delete.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_services')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $ok = $this->services->delete($validated['id']);

        return $ok
            ? Response::structured(['deleted' => true, 'id' => $validated['id']])
            : Response::error("Could not delete service {$validated['id']} (it may not exist).");
    }
}
