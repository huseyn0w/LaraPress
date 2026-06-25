<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Direct coverage for ChangePasswordRequest validation rules.
 *
 * The request requires an authenticated user (authorize() checks Auth::check()),
 * so we drive tests through the actual profile change-password route
 * (PUT /profile/change_password) rather than instantiating the FormRequest in
 * isolation — this keeps the test grounded in real HTTP behaviour.
 */
class ChangePasswordRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
        $this->user = User::factory()->create();
    }

    /**
     * A supplied password that is too short triggers a validation error.
     * (The password field is not required — when omitted the service handles it
     * via the current_password check; only presence + length + match are validated.)
     */
    public function test_short_password_fails_validation(): void
    {
        $response = $this->actingAs($this->user)
            ->from(route('get_change_password_interface'))
            ->put(route('change_password_action'), [
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /** Mismatched confirmation is rejected (same:password_confirmation rule). */
    public function test_mismatched_confirmation_fails_validation(): void
    {
        $response = $this->actingAs($this->user)
            ->from(route('get_change_password_interface'))
            ->put(route('change_password_action'), [
                'password' => 'newpassword123',
                'password_confirmation' => 'different456',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /** A missing confirmation is rejected. */
    public function test_missing_confirmation_fails_validation(): void
    {
        $response = $this->actingAs($this->user)
            ->from(route('get_change_password_interface'))
            ->put(route('change_password_action'), [
                'password' => 'newpassword123',
                // no `password_confirmation`
            ]);

        $response->assertSessionHasErrors('password');
    }

    /** A valid matching password pair passes validation. */
    public function test_valid_payload_passes_validation(): void
    {
        $response = $this->actingAs($this->user)
            ->from(route('get_change_password_interface'))
            ->put(route('change_password_action'), [
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertSessionDoesntHaveErrors(['password', 'password_confirmation']);
    }
}
