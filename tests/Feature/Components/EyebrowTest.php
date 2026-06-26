<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders slot content', function () {
    $html = Blade::render('<x-eyebrow>Category</x-eyebrow>');
    expect($html)->toContain('Category');
});

it('applies mono uppercase letter-spaced classes', function () {
    $html = Blade::render('<x-eyebrow>Label</x-eyebrow>');
    expect($html)->toContain('font-mono')
        ->toContain('uppercase')
        ->toContain('text-muted');
});

it('merges additional classes', function () {
    $html = Blade::render('<x-eyebrow class="mb-2">Label</x-eyebrow>');
    expect($html)->toContain('mb-2');
});
