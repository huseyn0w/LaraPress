<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Models\CPanel\CPanelGeneralSettings;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

/**
 * P8: the membership toggle and email-verification flow must stay coherent with
 * social login. New social accounts count as signups (blocked when membership is
 * off) while existing linked accounts can still log in; and a social account is
 * created already email-verified (the provider vouches for the address).
 */
class SocialMembershipAndVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function fakeSocialUser(string $email, string $id, string $name = 'Social User'): void
    {
        $socialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);
        $socialUser->shouldReceive('getId')->andReturn($id);
        $socialUser->id = $id;
        $socialUser->email = $email;
        $socialUser->name = $name;

        $provider = Mockery::mock();
        $provider->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->andReturn($provider);
    }

    private function setMembership(bool $on): void
    {
        CPanelGeneralSettings::query()->update(['membership' => $on ? 1 : 0]);
    }

    public function test_new_social_account_blocked_when_membership_off(): void
    {
        $this->setMembership(false);
        $countBefore = User::count();
        $this->fakeSocialUser('newsocial@example.com', 'gh-77777', 'New Social');

        $response = app(LoginController::class)->handleProviderCallback('github');

        $this->assertSame($countBefore, User::count(), 'No new social account when membership off');
        $this->assertDatabaseMissing('users', ['email' => 'newsocial@example.com']);
        $this->assertGuest();
        $this->assertStringContainsString('login', $response->getTargetUrl());
    }

    public function test_existing_social_user_can_login_when_membership_off(): void
    {
        $this->setMembership(false);
        $existing = User::factory()->create([
            'email' => 'returning@example.com', 'provider' => null, 'provider_id' => null,
        ]);
        $this->fakeSocialUser('returning@example.com', 'gh-88888');

        app(LoginController::class)->handleProviderCallback('github');

        $this->assertTrue(auth()->check());
        $this->assertSame($existing->id, auth()->id());
    }

    public function test_new_social_user_is_marked_email_verified(): void
    {
        $this->setMembership(true);
        $this->fakeSocialUser('verified-social@example.com', 'gh-66666');

        app(LoginController::class)->handleProviderCallback('github');

        $created = User::where('email', 'verified-social@example.com')->firstOrFail();
        $this->assertTrue($created->hasVerifiedEmail(), 'Social accounts are provider-verified');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
