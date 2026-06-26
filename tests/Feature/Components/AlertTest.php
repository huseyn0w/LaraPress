<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders info alert with role=status by default', function () {
    $html = Blade::render('<x-alert>Information message</x-alert>');
    expect($html)->toContain('Information message')
        ->toContain('role="status"');
});

it('renders error alert with role=alert', function () {
    $html = Blade::render('<x-alert variant="error">Something went wrong</x-alert>');
    expect($html)->toContain('role="alert"')
        ->toContain('bg-error-bg')
        ->toContain('text-error');
});

it('renders success alert', function () {
    $html = Blade::render('<x-alert variant="success">Saved!</x-alert>');
    expect($html)->toContain('bg-success-bg')
        ->toContain('text-success')
        ->toContain('role="status"');
});

it('renders warning alert', function () {
    $html = Blade::render('<x-alert variant="warning">Check this</x-alert>');
    expect($html)->toContain('bg-warning-bg')
        ->toContain('text-warning');
});

it('renders dismiss button when dismissible', function () {
    $html = Blade::render('<x-alert :dismissible="true">Dismissible</x-alert>');
    expect($html)->toContain('x-data')
        ->toContain('x-show')
        ->toContain('Dismiss');
});

it('renders rounded-md class', function () {
    $html = Blade::render('<x-alert>Test</x-alert>');
    expect($html)->toContain('rounded-md');
});

it('renders leading icon', function () {
    $html = Blade::render('<x-alert variant="info">Info</x-alert>');
    expect($html)->toContain('<svg');
});
