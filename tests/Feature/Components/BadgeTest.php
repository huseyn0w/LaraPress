<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders neutral badge by default', function () {
    $html = Blade::render('<x-badge>Draft</x-badge>');
    expect($html)->toContain('Draft')
        ->toContain('bg-surface-2')
        ->toContain('rounded-full');
});

it('renders primary badge', function () {
    $html = Blade::render('<x-badge variant="primary">Active</x-badge>');
    expect($html)->toContain('bg-primary')
        ->toContain('text-primary-contrast');
});

it('renders success badge', function () {
    $html = Blade::render('<x-badge variant="success">Published</x-badge>');
    expect($html)->toContain('bg-success-bg')
        ->toContain('text-success');
});

it('renders warning badge', function () {
    $html = Blade::render('<x-badge variant="warning">Pending</x-badge>');
    expect($html)->toContain('bg-warning-bg')
        ->toContain('text-warning');
});

it('renders error badge', function () {
    $html = Blade::render('<x-badge variant="error">Error</x-badge>');
    expect($html)->toContain('bg-error-bg')
        ->toContain('text-error');
});

it('has correct pill dimensions', function () {
    $html = Blade::render('<x-badge>Label</x-badge>');
    expect($html)->toContain('px-2.5')
        ->toContain('h-[22px]')
        ->toContain('text-xs')
        ->toContain('font-medium');
});
