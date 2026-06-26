<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders an svg with aria-hidden by default', function () {
    $html = Blade::render('<x-icon name="menu" />');
    expect($html)->toContain('aria-hidden="true"')
        ->toContain('<svg');
});

it('renders aria-label when label is provided', function () {
    $html = Blade::render('<x-icon name="close" label="Close menu" />');
    expect($html)->toContain('aria-label="Close menu"')
        ->not->toContain('aria-hidden');
});

it('passes extra attributes like class and width', function () {
    $html = Blade::render('<x-icon name="search" class="text-muted" width="24" />');
    expect($html)->toContain('class="text-muted"')
        ->toContain('width="24"');
});

it('renders known icons without errors', function (string $name) {
    $html = Blade::render('<x-icon :name="$name" />', ['name' => $name]);
    expect($html)->toContain('<svg');
})->with(['menu', 'close', 'search', 'sun', 'moon', 'chevron-down', 'check', 'heart', 'user', 'upload', 'grip']);
