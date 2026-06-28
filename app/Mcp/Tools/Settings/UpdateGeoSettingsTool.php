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

#[Description('Update the global GEO / business-identity settings singleton. Only the fields you pass are changed. Accepts business identity, services list, FAQ entries (one "Question | Answer" per line), sameAs URLs, and JSON-LD / llms.txt toggle flags. Requires the manage_general_settings permission.')]
class UpdateGeoSettingsTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelGeoSettingsRepository $geo) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'business_name' => $schema->string()->description('Legal or trading name of the business.'),
            'business_type' => $schema->string()->description('Schema.org type: Organization, LocalBusiness, ProfessionalService, or Person.'),
            'description' => $schema->string()->description('Short description of the business (up to 1000 characters).'),
            'founder_name' => $schema->string()->description('Founder or primary contact name.'),
            'services' => $schema->string()->description('Newline-separated list of services offered.'),
            'service_area' => $schema->string()->description('Geographic area served (city, country, etc.).'),
            'contact_email' => $schema->string()->description('Public contact email address.'),
            'contact_phone' => $schema->string()->description('Public contact phone number.'),
            'address' => $schema->string()->description('Physical address of the business.'),
            'same_as' => $schema->string()->description('Newline-separated authority/profile URLs (LinkedIn, Wikipedia, Wikidata, etc.) for sameAs.'),
            'faq' => $schema->string()->description('Newline-separated FAQ entries in "Question | Answer" format.'),
            'emit_jsonld' => $schema->boolean()->description('When true, the homepage emits schema.org JSON-LD using these values.'),
            'include_in_llms' => $schema->boolean()->description('When true, business identity is included in /llms.txt for AI crawlers.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $validated = $request->validate([
            'business_name' => ['nullable', 'string', 'max:255'],
            'business_type' => ['nullable', 'string', 'in:Organization,LocalBusiness,ProfessionalService,Person'],
            'description' => ['nullable', 'string', 'max:1000'],
            'founder_name' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'string', 'max:2000'],
            'service_area' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'same_as' => ['nullable', 'string', 'max:2000'],
            'faq' => ['nullable', 'string', 'max:5000'],
            'emit_jsonld' => ['nullable', 'boolean'],
            'include_in_llms' => ['nullable', 'boolean'],
        ]);

        $validated = array_filter($validated, fn ($v) => ! is_null($v));

        if (empty($validated)) {
            return Response::error('Nothing to update: provide at least one GEO setting to change.');
        }

        $instance = $this->geo->firstOrNew();
        $instance->fill($validated);
        $ok = $instance->save();

        return $ok
            ? Response::structured(['updated' => true, 'fields' => array_keys($validated)])
            : Response::error('Could not update GEO settings.');
    }
}
