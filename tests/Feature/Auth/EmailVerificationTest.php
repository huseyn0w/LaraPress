<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Models\CPanel\CPanelGeneralSettings;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * P8: email verification is OPTIONAL (the "email_verification" general setting)
 * but ENFORCED when enabled. Off (default): members are unaffected and no
 * verification email is sent on signup. On: signup sends a verification email
 * and unverified members are barred from member-only areas until they verify.
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    private function setVerification(bool $on): void
    {
        CPanelGeneralSettings::query()->update(['email_verification' => $on ? 1 : 0]);
    }

    private function unverifiedUser(): User
    {
        return User::factory()->create(['role_id' => 2, 'email_verified_at' => null]);
    }

    // --- OFF (default) ----------------------------------------------------

    public function test_off_unverified_member_can_access_member_area(): void
    {
        $this->setVerification(false);

        $this->actingAs($this->unverifiedUser())
            ->get(route('get_user_info'))
            ->assertStatus(200);
    }

    public function test_off_no_verification_email_sent_on_register(): void
    {
        Notification::fake();
        $this->setVerification(false);

        $this->post('/register', [
            'name' => 'Off Person', 'username' => 'offp',
            'email' => 'offp@example.com', 'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/');

        Notification::assertNothingSent();
    }

    // --- ON ---------------------------------------------------------------

    public function test_on_sends_verification_email_on_register(): void
    {
        Notification::fake();
        $this->setVerification(true);

        $this->post('/register', [
            'name' => 'On Person', 'username' => 'onp',
            'email' => 'onp@example.com', 'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/');

        $user = User::where('email', 'onp@example.com')->firstOrFail();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_on_blocks_unverified_member_from_member_area(): void
    {
        $this->setVerification(true);

        $this->actingAs($this->unverifiedUser())
            ->get(route('get_user_info'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_on_allows_verified_member(): void
    {
        $this->setVerification(true);
        $verified = User::factory()->create(['role_id' => 2, 'email_verified_at' => now()]);

        $this->actingAs($verified)
            ->get(route('get_user_info'))
            ->assertStatus(200);
    }

    public function test_verification_notice_page_renders_for_unverified(): void
    {
        $this->setVerification(true);

        $this->actingAs($this->unverifiedUser())
            ->get(route('verification.notice'))
            ->assertStatus(200);
    }
}
