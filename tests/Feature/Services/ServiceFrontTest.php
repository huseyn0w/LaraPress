<?php

use App\Http\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publishService(string $slug, int $status = 1, int $order = 0): Service
{
    $s = Service::create(['sort_order' => $order]);
    $t = $s->translateOrNew('en');
    $t->title = ucfirst($slug);
    $t->slug = $slug;
    $t->excerpt = 'Summary of '.$slug;
    $t->content = '<p>Body of '.$slug.'</p>';
    $t->status = $status;
    $s->save();

    return $s;
}

it('shows the services index with published services only', function () {
    publishService('alpha', 1);
    publishService('hidden', 0);

    $this->get('/services')
        ->assertOk()
        ->assertSee('Alpha')
        ->assertDontSee('Hidden');
});

it('shows a single published service by slug', function () {
    publishService('beta', 1);

    $this->get('/services/beta')->assertOk()->assertSee('Beta');
});

it('404s on a draft service detail', function () {
    publishService('secret', 0);

    $this->get('/services/secret')->assertNotFound();
});

it('orders the index grid by sort_order', function () {
    publishService('second', 1, 2);
    publishService('first', 1, 1);

    $body = $this->get('/services')->assertOk()->getContent();

    expect(strpos($body, 'First'))->toBeLessThan(strpos($body, 'Second'));
});
