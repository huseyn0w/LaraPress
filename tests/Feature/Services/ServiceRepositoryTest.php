<?php

use App\Http\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repo = app(ServiceRepository::class);
});

function makeService(int $order, string $slug, int $status): Service
{
    $s = Service::create(['sort_order' => $order]);
    $t = $s->translateOrNew('en');
    $t->title = ucfirst($slug);
    $t->slug = $slug;
    $t->status = $status;
    $s->save();

    return $s;
}

it('returns only published services ordered by sort_order', function () {
    makeService(2, 'second', Service::STATUS_PUBLISHED);
    makeService(1, 'first', Service::STATUS_PUBLISHED);
    makeService(0, 'draft', 0);

    $result = $this->repo->publishedOrdered('en');

    expect($result)->toHaveCount(2)
        ->and($result->pluck('slug')->all())->toBe(['first', 'second']);
});

it('lists published services in sitemap entries', function () {
    makeService(0, 'visible', Service::STATUS_PUBLISHED);
    makeService(0, 'hidden', 0);

    $slugs = $this->repo->sitemapEntries()->pluck('slug')->all();

    expect($slugs)->toContain('visible')
        ->and($slugs)->not->toContain('hidden');
});
