<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders a nav with aria-label Breadcrumb', function () {
    $html = Blade::render('<x-breadcrumb><x-breadcrumb.item href="/">Home</x-breadcrumb.item></x-breadcrumb>');
    expect($html)
        ->toContain('aria-label="Breadcrumb"')
        ->toContain('<nav');
});

it('renders an ordered list with mono text-xs text-muted', function () {
    $html = Blade::render('<x-breadcrumb><x-breadcrumb.item href="/">Home</x-breadcrumb.item></x-breadcrumb>');
    expect($html)
        ->toContain('<ol')
        ->toContain('font-mono')
        ->toContain('text-xs')
        ->toContain('text-muted');
});

it('renders item as a link when href is provided and not current', function () {
    $html = Blade::render('<x-breadcrumb><x-breadcrumb.item href="/blog">Blog</x-breadcrumb.item></x-breadcrumb>');
    expect($html)
        ->toContain('<a')
        ->toContain('href="/blog"')
        ->toContain('Blog');
});

it('renders current item as span with aria-current page', function () {
    $html = Blade::render('<x-breadcrumb><x-breadcrumb.item :current="true">Current Page</x-breadcrumb.item></x-breadcrumb>');
    expect($html)
        ->toContain('aria-current="page"')
        ->toContain('<span')
        ->toContain('Current Page')
        ->not->toContain('<a');
});

it('renders link with text-muted and hover:text-fg classes', function () {
    $html = Blade::render('<x-breadcrumb><x-breadcrumb.item href="/about">About</x-breadcrumb.item></x-breadcrumb>');
    expect($html)
        ->toContain('text-muted')
        ->toContain('hover:text-fg');
});

it('renders current item with text-fg class', function () {
    $html = Blade::render('<x-breadcrumb><x-breadcrumb.item :current="true">Now</x-breadcrumb.item></x-breadcrumb>');
    expect($html)->toContain('text-fg');
});

it('renders separator aria-hidden for non-first items', function () {
    $html = Blade::render(
        '<x-breadcrumb>
            <x-breadcrumb.item href="/">Home</x-breadcrumb.item>
            <x-breadcrumb.item href="/blog">Blog</x-breadcrumb.item>
            <x-breadcrumb.item :current="true">Post</x-breadcrumb.item>
        </x-breadcrumb>'
    );
    expect($html)->toContain('aria-hidden');
});
