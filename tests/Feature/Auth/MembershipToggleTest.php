<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Models\CPanel\CPanelGeneralSettings;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * P8: the "membership" general setting gates self-service registration. When it
 * is off, the register routes are blocked and the header hides the register
 * link; login is unaffected. Default (seeded) is on.
 */
class MembershipToggleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    private function setMembership(bool $on): void
    {
        CPanelGeneralSettings::query()->update(['membership' => $on ? 1 : 0]);
    }

    public function test_registration_open_by_default(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_register_page_blocked_when_membership_off(): void
    {
        $this->setMembership(false);

        $this->get('/register')->assertRedirect(route('login'));
    }

    public function test_register_post_blocked_when_membership_off(): void
    {
        $this->setMembership(false);

        $this->post('/register', [
            'name' => 'Blocked Person',
            'username' => 'blocked',
            'email' => 'blocked@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('users', ['email' => 'blocked@example.com']);
        $this->assertGuest();
    }

    public function test_register_allowed_when_membership_on(): void
    {
        $this->setMembership(true);

        $this->post('/register', [
            'name' => 'Allowed Person',
            'username' => 'allowed',
            'email' => 'allowed@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('users', ['email' => 'allowed@example.com']);
    }

    public function test_login_unaffected_when_membership_off(): void
    {
        $this->setMembership(false);

        $this->get('/login')->assertStatus(200);
    }

    public function test_header_hides_register_link_when_membership_off(): void
    {
        $this->setMembership(false);
        $html = $this->get('/')->getContent();
        $this->assertStringNotContainsString(route('register'), $html);
    }

    public function test_header_shows_register_link_when_membership_on(): void
    {
        $this->setMembership(true);
        $html = $this->get('/')->getContent();
        $this->assertStringContainsString(route('register'), $html);
    }
}
