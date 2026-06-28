<?php

use App\Http\Models\Service;
use App\Services\CPanel\CPanelServiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('runs a restore bulk action through the service', function () {
    $svc = app(CPanelServiceService::class);
    $service = Service::create(['sort_order' => 0]);
    $service->delete();

    $svc->runBulkAction('restore', [$service->id]);

    expect(Service::find($service->id))->not->toBeNull();
});

it('runs a destroy bulk action through the service', function () {
    $svc = app(CPanelServiceService::class);
    $service = Service::create(['sort_order' => 0]);
    $service->delete();

    $svc->runBulkAction('destroy', [$service->id]);

    expect(Service::withTrashed()->find($service->id))->toBeNull();
});
