<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders primary button with correct classes', function () {
    $html = Blade::render('<x-button>Save</x-button>');
    expect($html)->toContain('Save')
        ->toContain('bg-primary')
        ->toContain('text-primary-contrast')
        ->toContain('rounded-md');
});

it('sets aria-busy when loading', function () {
    $html = Blade::render('<x-button :loading="true">Save</x-button>');
    expect($html)->toContain('aria-busy="true"')
        ->toContain('Save')
        ->toContain('animate-spin');
});

it('renders focus-visible ring classes', function () {
    $html = Blade::render('<x-button>Click</x-button>');
    expect($html)->toContain('focus-visible:ring-2')
        ->toContain('focus-visible:ring-ring')
        ->toContain('focus-visible:ring-offset-2');
});

it('renders secondary variant', function () {
    $html = Blade::render('<x-button variant="secondary">Click</x-button>');
    expect($html)->toContain('bg-surface-2')
        ->toContain('text-fg');
});

it('renders outline variant', function () {
    $html = Blade::render('<x-button variant="outline">Click</x-button>');
    expect($html)->toContain('border-strong')
        ->toContain('bg-transparent');
});

it('renders ghost variant', function () {
    $html = Blade::render('<x-button variant="ghost">Click</x-button>');
    expect($html)->toContain('bg-transparent');
});

it('renders destructive variant', function () {
    $html = Blade::render('<x-button variant="destructive">Delete</x-button>');
    expect($html)->toContain('bg-error');
});

it('renders sm size', function () {
    $html = Blade::render('<x-button size="sm">Small</x-button>');
    expect($html)->toContain('h-8')->toContain('px-3');
});

it('renders lg size', function () {
    $html = Blade::render('<x-button size="lg">Large</x-button>');
    expect($html)->toContain('h-12')->toContain('px-6');
});

it('renders as anchor when href is provided', function () {
    $html = Blade::render('<x-button href="/go">Link</x-button>');
    expect($html)->toContain('<a')
        ->toContain('href="/go"')
        ->toContain('Link');
});

it('renders active scale class for reduced motion', function () {
    $html = Blade::render('<x-button>Click</x-button>');
    expect($html)->toContain('active:scale-[0.98]');
});

it('renders disabled styles', function () {
    $html = Blade::render('<x-button>Click</x-button>');
    expect($html)->toContain('disabled:cursor-not-allowed')
        ->toContain('disabled:opacity-50');
});
