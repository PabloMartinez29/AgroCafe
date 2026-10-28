<?php

namespace Tests\Feature\Auth;

use App\Models\ConsentRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'accept_terms' => '1',
            'accept_privacy' => '1',
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_screen_shows_two_separate_unchecked_consent_checkboxes_with_links(): void
    {
        $response = $this->get('/register');

        $response->assertSee(route('legal.terms'), false);
        $response->assertSee(route('legal.privacy'), false);

        foreach (['accept_terms', 'accept_privacy'] as $field) {
            $this->assertSame(1, preg_match('/<input[^>]*name="'.$field.'"[^>]*>/s', $response->getContent(), $input));
            $this->assertStringContainsString('type="checkbox"', $input[0]);
            $this->assertStringContainsString('required', $input[0]);
            $this->assertStringNotContainsString('checked', $input[0]);
        }
    }

    public function test_registration_screen_only_asks_for_the_data_it_needs(): void
    {
        $response = $this->get('/register');

        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertDontSee('name="phone"', false);
        $response->assertDontSee('name="address"', false);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', $this->validPayload());

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_stores_the_proof_of_each_authorization(): void
    {
        $this->withHeader('User-Agent', 'PHPUnit-Browser/1.0')
            ->post('/register', $this->validPayload());

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $records = ConsentRecord::query()->where('subject_id', $user->id)->orderBy('id')->get();

        $this->assertCount(2, $records);
        $this->assertSame(['terms', 'privacy'], $records->pluck('document')->all());

        foreach ($records as $record) {
            $this->assertSame(User::class, $record->subject_type);
            $this->assertSame('test@example.com', $record->subject_email);
            $this->assertSame('1.0', $record->document_version);
            $this->assertSame(64, strlen($record->document_hash));
            $this->assertSame(ConsentRecord::ACTION_GRANTED, $record->action);
            $this->assertSame(ConsentRecord::CHANNEL_WEB_REGISTER, $record->channel);
            $this->assertSame('PHPUnit-Browser/1.0', $record->user_agent);
            $this->assertNotNull($record->ip_address);
            $this->assertNull($record->captured_by_id);
            $this->assertNotEmpty($record->consent_text);
            $this->assertSame($record->computeHash(), $record->record_hash);
        }
    }

    public function test_registration_is_rejected_without_accepting_the_terms(): void
    {
        $response = $this->post('/register', $this->validPayload(['accept_terms' => null]));

        $response->assertSessionHasErrors('accept_terms');
        $response->assertSessionDoesntHaveErrors('accept_privacy');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('consent_records', 0);
    }

    public function test_registration_is_rejected_without_accepting_the_privacy_policy(): void
    {
        $response = $this->post('/register', $this->validPayload(['accept_privacy' => null]));

        $response->assertSessionHasErrors('accept_privacy');
        $response->assertSessionDoesntHaveErrors('accept_terms');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_optional_phone_and_address_are_not_required(): void
    {
        $this->post('/register', $this->validPayload());

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertNull($user->phone);
        $this->assertNull($user->address);
    }
}
