<?php

use App\Http\Models\Service;
use App\Repositories\CPanelServiceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('soft-deletes then restores a service', function () {
    $repo = app(CPanelServiceRepository::class);
    $service = Service::create(['sort_order' => 0]);

    $repo->delete($service->id);
    expect(Service::find($service->id))->toBeNull();

    $repo->restore($service->id);
    expect(Service::find($service->id))->not->toBeNull();
});

it('permanently destroys a trashed service', function () {
    $repo = app(CPanelServiceRepository::class);
    $service = Service::create(['sort_order' => 0]);

    $repo->delete($service->id);
    $repo->destroy($service->id);

    expect(Service::withTrashed()->find($service->id))->toBeNull();
});

it('deletes a batch of services by array of ids', function () {
    $repo = app(CPanelServiceRepository::class);
    $a = Service::create(['sort_order' => 0]);
    $b = Service::create(['sort_order' => 1]);

    $repo->delete([$a->id, $b->id]);

    expect(Service::count())->toBe(0)
        ->and(Service::withTrashed()->count())->toBe(2);
});
