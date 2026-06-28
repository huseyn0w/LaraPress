<?php

namespace App\Mcp\Tools\Settings;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelGeoSettingsRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. Return the global GEO / business-identity settings: business name, type, description, founder, services, service area, contact info, address, sameAs links, FAQ entries, and JSON-LD / llms.txt toggle flags. Requires the manage_general_settings permission.')]
class GetGeoSettingsTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelGeoSettingsRepository $geo) {}

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $row = $this->geo->firstOrNew();

        return Response::structured([
            'business_name' => $row->business_name ?? null,
            'business_type' => $row->business_type ?? null,
            'description' => $row->description ?? null,
            'founder_name' => $row->founder_name ?? null,
            'services' => $row->services ?? null,
            'service_area' => $row->service_area ?? null,
            'contact_email' => $row->contact_email ?? null,
            'contact_phone' => $row->contact_phone ?? null,
            'address' => $row->address ?? null,
            'same_as' => $row->same_as ?? null,
            'faq' => $row->faq ?? null,
            'emit_jsonld' => (bool) ($row->emit_jsonld ?? false),
            'include_in_llms' => (bool) ($row->include_in_llms ?? false),
        ]);
    }
}
