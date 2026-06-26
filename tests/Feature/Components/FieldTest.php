<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders label', function () {
    $html = Blade::render('<x-field label="Email" name="email"><input id="email" type="email" /></x-field>');
    expect($html)->toContain('Email')
        ->toContain('<label');
});

it('links label to control via for attribute', function () {
    $html = Blade::render('<x-field label="Email" name="email"><input id="email" /></x-field>');
    expect($html)->toContain('for="email"');
});

it('renders help text', function () {
    $html = Blade::render('<x-field label="Name" name="name" help="Your full name"><input /></x-field>');
    expect($html)->toContain('Your full name');
});

it('renders error message', function () {
    $html = Blade::render('<x-field label="Email" name="email" error="Invalid email"><input /></x-field>');
    expect($html)->toContain('Invalid email')
        ->toContain('text-error');
});

it('sets error id for aria-describedby', function () {
    $html = Blade::render('<x-field label="Email" name="email" error="Bad email"><input /></x-field>');
    expect($html)->toContain('id="email-error"');
});

it('marks required fields', function () {
    $html = Blade::render('<x-field label="Email" name="email" :required="true"><input /></x-field>');
    expect($html)->toContain('required')
        ->toContain('sr-only');
});

it('renders slot content', function () {
    $html = Blade::render('<x-field label="Test" name="test"><input id="test" class="my-input" /></x-field>');
    expect($html)->toContain('my-input');
});
