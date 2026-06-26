<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders an article element', function () {
    $html = Blade::render('<x-card.post title="Hello World" url="/posts/hello" />');
    expect($html)->toContain('<article');
});

it('renders a linked title with font-serif', function () {
    $html = Blade::render('<x-card.post title="My Post" url="/posts/my-post" />');
    expect($html)
        ->toContain('My Post')
        ->toContain('/posts/my-post')
        ->toContain('font-serif')
        ->toContain('text-xl');
});

it('renders excerpt when provided', function () {
    $html = Blade::render('<x-card.post title="T" url="/t" excerpt="Short description here" />');
    expect($html)
        ->toContain('Short description here')
        ->toContain('text-muted')
        ->toContain('text-sm');
});

it('does not render excerpt markup when absent', function () {
    $html = Blade::render('<x-card.post title="T" url="/t" />');
    expect($html)->not->toContain('text-muted text-sm');
});

it('renders category as eyebrow when provided', function () {
    $html = Blade::render('<x-card.post title="T" url="/t" category="Technology" />');
    expect($html)->toContain('Technology');
});

it('renders date as time element when provided', function () {
    $html = Blade::render('<x-card.post title="T" url="/t" date="2024-01-15" />');
    expect($html)
        ->toContain('<time')
        ->toContain('2024-01-15');
});

it('renders author when provided', function () {
    $html = Blade::render('<x-card.post title="T" url="/t" author="Jane Doe" />');
    expect($html)->toContain('Jane Doe');
});

it('renders image with lazy loading and 16:9 aspect when provided', function () {
    $html = Blade::render('<x-card.post title="T" url="/t" image="/img/post.jpg" />');
    expect($html)
        ->toContain('<img')
        ->toContain('/img/post.jpg')
        ->toContain('loading="lazy"')
        ->toContain('rounded-md');
});

it('does not render image tag when image is absent', function () {
    $html = Blade::render('<x-card.post title="T" url="/t" />');
    expect($html)->not->toContain('<img');
});

it('has mono text-xs text-subtle classes on the meta row', function () {
    $html = Blade::render('<x-card.post title="T" url="/t" date="2024-01-15" author="Bob" />');
    expect($html)
        ->toContain('font-mono')
        ->toContain('text-xs')
        ->toContain('text-subtle');
});
