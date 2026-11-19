<?php

namespace Tests\Feature;

use App\Enums\NotificationType;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function registrationPayload(array $overrides = []): array
    {
        return [
            'name'                  => 'Ada Lovelace',
            'email'                 => 'ada@example.com',
            'password'              => 'correct-horse',
            'password_confirmation' => 'correct-horse',
            ...$overrides,
        ];
    }

    public function test_a_user_can_register_and_receives_a_token(): void
    {
        $response = $this->postJson('/api/register', $this->registrationPayload());

        $response->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'email']]);
        $this->assertDatabaseHas('users', ['email' => 'ada@example.com']);
        $this->assertNotSame('correct-horse', User::first()->password, 'password must be hashed');
    }

    public function test_registering_creates_default_notification_preferences_and_queues_a_welcome_email(): void
    {
        Mail::fake();

        $this->postJson('/api/register', $this->registrationPayload())->assertOk();

        $user = User::where('email', 'ada@example.com')->first();
        $this->assertSame(count(NotificationType::cases()), $user->notificationTypes()->count());
        $this->assertSame(0, $user->notificationTypes()->where('mail', false)->count());
        Mail::assertQueued(WelcomeMail::class);
    }

    public function test_registration_validates_input(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/register', $this->registrationPayload([
            'email'                 => 'taken@example.com',
            'password_confirmation' => 'does-not-match',
        ]))->assertStatus(422)->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_a_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'ada@example.com']);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_login_fails_with_a_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'nope'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_a_google_only_account_is_told_to_use_google_sign_in(): void
    {
        $user = User::factory()->create(['password' => null, 'google_id' => 'g-123']);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'anything'])
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'This account uses Google Sign-In. Please login with Google.');
    }

    public function test_protected_routes_require_authentication(): void
    {
        $this->getJson('/api/time-entries')->assertUnauthorized();
        $this->getJson('/api/channels')->assertUnauthorized();
        $this->getJson('/api/notifications')->assertUnauthorized();
    }

    public function test_logout_revokes_the_users_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('api-token');
        Sanctum::actingAs($user);

        $this->postJson('/api/logout')->assertOk();

        $this->assertSame(0, $user->tokens()->count());
    }
}
