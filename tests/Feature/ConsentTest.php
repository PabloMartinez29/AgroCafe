<?php

namespace Tests\Feature;

use App\Models\ConsentRecord;
use App\Models\Cooperative;
use App\Models\User;
use App\Services\ConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use LogicException;
use Tests\TestCase;

class ConsentTest extends TestCase
{
    use RefreshDatabase;

    private function consents(): ConsentService
    {
        return app(ConsentService::class);
    }

    /**
     * @return array<string, string>
     */
    private function acceptances(): array
    {
        return ['accept_terms' => '1', 'accept_privacy' => '1'];
    }

    public function test_records_are_chained_and_the_chain_verifies(): void
    {
        $user = User::factory()->create();

        $this->consents()->record($user, Request::create('/'), ConsentRecord::CHANNEL_WEB_REGISTER);
        $this->consents()->record($user, Request::create('/'), ConsentRecord::CHANNEL_WEB_ACCEPT);

        $records = ConsentRecord::query()->orderBy('id')->get();

        $this->assertCount(4, $records);
        $this->assertNull($records[0]->previous_hash);
        $this->assertSame($records[0]->record_hash, $records[1]->previous_hash);
        $this->assertSame($records[2]->record_hash, $records[3]->previous_hash);

        $result = $this->consents()->verifyChain();

        $this->assertTrue($result['valid']);
        $this->assertSame(4, $result['checked']);
    }

    public function test_a_modified_record_breaks_the_chain(): void
    {
        $this->consents()->record(User::factory()->create(), Request::create('/'), ConsentRecord::CHANNEL_WEB_REGISTER);

        $firstId = ConsentRecord::query()->min('id');

        DB::table('consent_records')->where('id', $firstId)->update(['ip_address' => '6.6.6.6']);

        $result = $this->consents()->verifyChain();

        $this->assertFalse($result['valid']);
        $this->assertSame($firstId, $result['broken_id']);
        $this->artisan('consents:verify')->assertFailed();
    }

    public function test_a_deleted_record_breaks_the_chain(): void
    {
        $this->consents()->record(User::factory()->create(), Request::create('/'), ConsentRecord::CHANNEL_WEB_REGISTER);

        DB::table('consent_records')->where('id', ConsentRecord::query()->min('id'))->delete();

        $this->assertFalse($this->consents()->verifyChain()['valid']);
    }

    public function test_records_cannot_be_updated_or_deleted_through_the_model(): void
    {
        $this->consents()->record(User::factory()->create(), Request::create('/'), ConsentRecord::CHANNEL_WEB_REGISTER);
        $record = ConsentRecord::query()->firstOrFail();

        try {
            $record->update(['ip_address' => '6.6.6.6']);
            $this->fail('El registro no debería poder modificarse.');
        } catch (LogicException) {
            $this->assertNotSame('6.6.6.6', $record->fresh()->ip_address);
        }

        $this->expectException(LogicException::class);
        $record->delete();
    }

    public function test_verify_command_succeeds_on_an_intact_chain(): void
    {
        $this->consents()->record(User::factory()->create(), Request::create('/'), ConsentRecord::CHANNEL_WEB_REGISTER);

        $this->artisan('consents:verify')->assertSuccessful();
    }

    public function test_evidence_command_shows_the_records_of_a_person(): void
    {
        $user = User::factory()->create(['email' => 'campesino@example.com']);
        $this->consents()->record($user, Request::create('/'), ConsentRecord::CHANNEL_WEB_REGISTER);

        $this->artisan('consents:evidence', ['email' => 'CAMPESINO@example.com'])
            ->expectsOutputToContain('Integridad de la cadena: OK')
            ->assertSuccessful();
    }

    public function test_evidence_command_fails_when_there_is_no_proof(): void
    {
        $this->artisan('consents:evidence', ['email' => 'nadie@example.com'])
            ->expectsOutputToContain('no puede probarse')
            ->assertFailed();
    }

    public function test_the_document_hash_ignores_line_ending_differences(): void
    {
        $path = view()->getFinder()->find(config('legal.documents.terms.view'));
        $normalized = hash('sha256', str_replace("\r\n", "\n", file_get_contents($path)));

        $this->assertSame($normalized, $this->consents()->documentHash('terms'));
    }

    public function test_users_without_a_record_are_sent_to_accept_the_documents(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('consent.show'));
        $this->actingAs($user)->get('/profile')->assertRedirect(route('consent.show'));
    }

    public function test_users_with_a_record_can_use_the_application(): void
    {
        $user = User::factory()->withConsents()->create();

        $this->actingAs($user)->get('/profile')->assertOk();
        $this->actingAs($user)->get(route('consent.show'))->assertRedirect(route('dashboard'));
    }

    public function test_the_acceptance_screen_shows_two_separate_checkboxes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('consent.show'));

        $response->assertOk();
        $response->assertSee('name="accept_terms"', false);
        $response->assertSee('name="accept_privacy"', false);
        $response->assertSee(route('legal.terms'), false);
        $response->assertSee(route('legal.privacy'), false);
    }

    public function test_accepting_the_documents_stores_the_proof_and_unlocks_the_application(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('consent.store'), $this->acceptances())
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertTrue($this->consents()->hasAcceptedAll($user));
        $this->assertSame(
            [ConsentRecord::CHANNEL_WEB_ACCEPT],
            ConsentRecord::query()->pluck('channel')->unique()->values()->all(),
        );
    }

    public function test_each_checkbox_is_required_on_the_acceptance_screen(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('consent.store'), ['accept_terms' => '1'])
            ->assertSessionHasErrors('accept_privacy');

        $this->actingAs($user)
            ->post(route('consent.store'), ['accept_privacy' => '1'])
            ->assertSessionHasErrors('accept_terms');

        $this->assertDatabaseCount('consent_records', 0);
    }

    public function test_a_new_document_version_requires_accepting_again(): void
    {
        $user = User::factory()->withConsents()->create();
        $this->assertTrue($this->consents()->hasAcceptedAll($user));

        config(['legal.documents.privacy.version' => '1.1']);

        $this->assertSame(['privacy'], $this->consents()->pendingDocuments($user));
        $this->actingAs($user)->get('/profile')->assertRedirect(route('consent.show'));
    }

    public function test_a_revocation_withdraws_the_authorization(): void
    {
        $user = User::factory()->withConsents()->create();

        $this->consents()->revokeAll($user, Request::create('/'));

        $this->assertFalse($this->consents()->hasAcceptedAll($user));
    }

    public function test_admin_created_users_are_recorded_as_assisted_but_must_accept_for_themselves(): void
    {
        $admin = User::factory()->admin()->withConsents()->create();

        $payload = [
            'name' => 'Campesino Nuevo',
            'email' => 'nuevo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'peasant',
            'active' => '1',
        ];

        $this->actingAs($admin)->post(route('admin.users.store'), $payload)
            ->assertSessionHasErrors(['accept_terms', 'accept_privacy']);
        $this->assertDatabaseMissing('users', ['email' => 'nuevo@example.com']);

        $this->actingAs($admin)->post(route('admin.users.store'), $payload + $this->acceptances())
            ->assertRedirect(route('admin.users.index'));

        $peasant = User::where('email', 'nuevo@example.com')->firstOrFail();
        $records = ConsentRecord::query()->where('subject_id', $peasant->id)->get();

        $this->assertCount(2, $records);
        $this->assertSame([ConsentRecord::CHANNEL_ADMIN_ASSISTED], $records->pluck('channel')->unique()->values()->all());
        $this->assertSame([$admin->id], $records->pluck('captured_by_id')->unique()->values()->all());
        $this->assertStringContainsString('Confirmo que', $records->first()->consent_text);

        $this->assertFalse($this->consents()->hasAcceptedAll($peasant));
        $this->actingAs($peasant)->get('/dashboard')->assertRedirect(route('consent.show'));
    }

    public function test_admin_created_cooperatives_require_and_record_the_authorization(): void
    {
        $admin = User::factory()->admin()->withConsents()->create();
        $payload = ['name' => 'Cooperativa Sur', 'nit' => '900111222-3'];

        $this->actingAs($admin)->post(route('admin.cooperatives.store'), $payload)
            ->assertSessionHasErrors(['accept_terms', 'accept_privacy']);
        $this->assertDatabaseCount('cooperatives', 0);

        $this->actingAs($admin)->post(route('admin.cooperatives.store'), $payload + $this->acceptances())
            ->assertRedirect(route('admin.cooperatives.index'));

        $cooperative = Cooperative::firstOrFail();

        $this->assertSame(2, ConsentRecord::query()
            ->where('subject_type', $cooperative->getMorphClass())
            ->where('subject_id', $cooperative->id)
            ->where('captured_by_id', $admin->id)
            ->count());
    }

    public function test_admin_forms_show_two_separate_checkboxes(): void
    {
        $admin = User::factory()->admin()->withConsents()->create();

        foreach (['admin.users.create', 'admin.cooperatives.create'] as $route) {
            $response = $this->actingAs($admin)->get(route($route));

            $response->assertOk();
            $response->assertSee('name="accept_terms"', false);
            $response->assertSee('name="accept_privacy"', false);
            $response->assertSee(route('legal.terms'), false);
            $response->assertSee(route('legal.privacy'), false);
        }
    }

    public function test_api_registration_requires_the_authorizations_and_never_creates_admins(): void
    {
        $payload = [
            'name' => 'Movil User',
            'email' => 'movil@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ];

        $this->postJson('/api/register', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['accept_terms', 'accept_privacy']);

        $this->postJson('/api/register', $payload + $this->acceptances())->assertCreated();

        $user = User::where('email', 'movil@example.com')->firstOrFail();

        $this->assertSame('peasant', $user->role);
        $this->assertSame(
            [ConsentRecord::CHANNEL_API_REGISTER],
            ConsentRecord::query()->where('subject_id', $user->id)->pluck('channel')->unique()->values()->all(),
        );
        $this->assertTrue($this->consents()->hasAcceptedAll($user));
    }

    public function test_api_users_without_a_record_are_blocked_until_they_accept(): void
    {
        $user = User::factory()->create();

        $login = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password']);
        $login->assertOk()->assertJson(['consents_pending' => ['terms', 'privacy']]);

        Sanctum::actingAs($user);

        $this->getJson('/api/cooperatives')
            ->assertForbidden()
            ->assertJson(['consents_pending' => ['terms', 'privacy']]);

        $this->postJson('/api/consents', $this->acceptances())->assertOk();

        $this->getJson('/api/cooperatives')->assertOk();
    }

    public function test_deleting_the_account_keeps_the_proof_and_records_the_revocation(): void
    {
        $user = User::factory()->withConsents()->create(['email' => 'borrar@example.com']);

        $this->actingAs($user)
            ->delete('/profile', ['password' => 'password'])
            ->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['email' => 'borrar@example.com']);

        $records = ConsentRecord::query()->where('subject_email', 'borrar@example.com')->orderBy('id')->get();

        $this->assertCount(4, $records);
        $this->assertSame(2, $records->where('action', ConsentRecord::ACTION_REVOKED)->count());
        $this->assertTrue($this->consents()->verifyChain()['valid']);
    }
}
