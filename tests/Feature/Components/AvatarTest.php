<?php

use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders rounded-full class', function () {
    $html = Blade::render('<x-avatar name="Alice" />');
    expect($html)->toContain('rounded-full');
});

it('renders initials when no image src is given', function () {
    $html = Blade::render('<x-avatar name="Alice" />');
    expect($html)
        ->toContain('A')
        ->toContain('bg-surface-2')
        ->toContain('text-muted');
});

it('renders img tag when src is provided', function () {
    $html = Blade::render('<x-avatar src="/img/user.jpg" name="Alice" />');
    expect($html)
        ->toContain('<img')
        ->toContain('/img/user.jpg')
        ->toContain('alt="Alice"');
});

it('renders sm size at 24px', function () {
    $html = Blade::render('<x-avatar name="A" size="sm" />');
    expect($html)->toContain('24');
});

it('renders md size at 32px', function () {
    $html = Blade::render('<x-avatar name="A" size="md" />');
    expect($html)->toContain('32');
});

it('renders lg size at 48px', function () {
    $html = Blade::render('<x-avatar name="A" size="lg" />');
    expect($html)->toContain('48');
});

it('derives name from user model', function () {
    $user = User::factory()->make(['name' => 'Maria']);
    $html = Blade::render('<x-avatar :user="$u" />', ['u' => $user]);
    expect($html)->toContain('M');
});

it('renders user avatar image when user has an avatar', function () {
    $user = User::factory()->make(['name' => 'Bob', 'avatar' => '/avatars/bob.jpg']);
    $html = Blade::render('<x-avatar :user="$u" />', ['u' => $user]);
    expect($html)
        ->toContain('<img')
        ->toContain('/avatars/bob.jpg');
});

it('falls back to initials when user has no avatar', function () {
    $user = User::factory()->make(['name' => 'Sam', 'avatar' => null]);
    $html = Blade::render('<x-avatar :user="$u" />', ['u' => $user]);
    expect($html)
        ->toContain('S')
        ->toContain('bg-surface-2');
});
