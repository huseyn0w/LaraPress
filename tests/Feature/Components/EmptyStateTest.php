<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders headline with font-serif text-xl', function () {
    $html = Blade::render('<x-empty-state headline="No posts yet" />');
    expect($html)
        ->toContain('No posts yet')
        ->toContain('font-serif')
        ->toContain('text-xl');
});

it('renders centered layout', function () {
    $html = Blade::render('<x-empty-state headline="Nothing here" />');
    expect($html)->toContain('text-center');
});

it('renders explanation slot when provided', function () {
    $html = Blade::render('<x-empty-state headline="Empty">Try creating one now.</x-empty-state>');
    expect($html)
        ->toContain('Try creating one now.')
        ->toContain('text-muted')
        ->toContain('text-sm');
});

it('renders icon when provided', function () {
    $html = Blade::render('<x-empty-state headline="Empty" icon="upload" />');
    expect($html)->toContain('<svg');
});

it('does not render icon markup when icon is absent', function () {
    $html = Blade::render('<x-empty-state headline="Empty" />');
    // No icon class on icon wrapper when icon is absent
    $html2 = Blade::render('<x-empty-state headline="Empty">Explanation</x-empty-state>');
    expect($html2)->toContain('Explanation');
});

it('renders CTA slot when provided', function () {
    $html = Blade::render(
        '<x-empty-state headline="No files">
            <x-slot:cta><x-button>Upload</x-button></x-slot:cta>
        </x-empty-state>'
    );
    expect($html)->toContain('Upload');
});
