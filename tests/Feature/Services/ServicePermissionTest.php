<?php

use App\Http\Models\User;
use App\Http\Models\UserRoles;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

it('grants manage_services to the seeded administrator role', function () {
    $this->seed(DatabaseSeeder::class);
    $admin = User::where('username', 'admin')->firstOrFail();
    Auth::login($admin);

    expect($admin->can('manage_services', UserRoles::class))->toBeTrue();
});

it('denies manage_services to a user holding the non-admin role', function () {
    $this->seed(DatabaseSeeder::class);
    // Role id 2 is the "User" role seeded with every permission flag = 0.
    $user = User::factory()->create(['role_id' => 2]);
    Auth::login($user);

    expect($user->can('manage_services', UserRoles::class))->toBeFalse();
});
