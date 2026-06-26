<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders a nav with aria-label Pagination', function () {
    $paginator = new LengthAwarePaginator([], 30, 10, 2);
    $html = Blade::render('<x-pagination :paginator="$p" />', ['p' => $paginator]);
    expect($html)
        ->toContain('aria-label="Pagination"')
        ->toContain('<nav');
});

it('shows current page and last page', function () {
    $paginator = new LengthAwarePaginator([], 30, 10, 2);
    $html = Blade::render('<x-pagination :paginator="$p" />', ['p' => $paginator]);
    expect($html)
        ->toContain('2')
        ->toContain('3');
});

it('renders Previous and Next labels', function () {
    $paginator = new LengthAwarePaginator([], 30, 10, 2);
    $html = Blade::render('<x-pagination :paginator="$p" />', ['p' => $paginator]);
    expect($html)
        ->toContain('Previous')
        ->toContain('Next');
});

it('disables Previous link on first page', function () {
    $paginator = new LengthAwarePaginator([], 30, 10, 1);
    $html = Blade::render('<x-pagination :paginator="$p" />', ['p' => $paginator]);
    expect($html)
        ->toContain('text-subtle')
        ->toContain('pointer-events-none');
});

it('disables Next link on last page', function () {
    $paginator = new LengthAwarePaginator([], 30, 10, 3, ['path' => '/']);
    $html = Blade::render('<x-pagination :paginator="$p" />', ['p' => $paginator]);
    expect($html)
        ->toContain('text-subtle')
        ->toContain('pointer-events-none');
});

it('renders with font-mono and text-xs', function () {
    $paginator = new LengthAwarePaginator([], 30, 10, 2);
    $html = Blade::render('<x-pagination :paginator="$p" />', ['p' => $paginator]);
    expect($html)
        ->toContain('font-mono')
        ->toContain('text-xs');
});
