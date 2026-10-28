<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_each_legal_document_is_public_and_shows_its_version(): void
    {
        $documents = [
            'legal.terms' => 'Términos y Condiciones',
            'legal.privacy' => 'Política de Privacidad',
            'legal.cookies' => 'Política de Cookies',
            'legal.refund' => 'Política de Reembolsos y Retracto',
        ];

        foreach ($documents as $route => $title) {
            $response = $this->get(route($route));

            $response->assertOk();
            $response->assertSee($title, false);
            $response->assertSee('Versión 1.0', false);
        }
    }

    public function test_the_privacy_policy_covers_what_the_colombian_data_law_requires(): void
    {
        $response = $this->get(route('legal.privacy'));

        $response->assertSee('Ley 1581 de 2012', false);
        $response->assertSee('Responsable del tratamiento', false);
        $response->assertSee('Tus derechos', false);
        $response->assertSee('Superintendencia de Industria y Comercio', false);
        $response->assertSee('10 días hábiles', false);
        $response->assertSee('15 días hábiles', false);
        $response->assertSee('prueba de la autorización', false);
        $response->assertSee('mailto:'.config('legal.controller.privacy_email'), false);
    }

    public function test_the_cookie_policy_lists_the_real_cookies_and_explains_consent(): void
    {
        $response = $this->get(route('legal.cookies'));

        $response->assertSee(config('session.cookie'), false);
        $response->assertSee('XSRF-TOKEN', false);
        $response->assertSee('remember_web_', false);
        $response->assertSee('No usamos cookies de publicidad', false);
        $response->assertSee('¿Necesitamos pedirte consentimiento?', false);
    }

    public function test_the_refund_policy_states_there_are_no_online_sales_by_default(): void
    {
        $response = $this->get(route('legal.refund'));

        $response->assertSee('no vende productos ni cobra servicios por internet', false);
        $response->assertDontSee('Derecho de retracto', false);
    }

    public function test_the_refund_policy_includes_the_withdrawal_right_when_selling_online(): void
    {
        config(['legal.sells_online' => true]);

        $response = $this->get(route('legal.refund'));

        $response->assertSee('Derecho de retracto', false);
        $response->assertSee('5 días hábiles', false);
        $response->assertSee('30 días calendario', false);
        $response->assertDontSee('no vende productos ni cobra servicios por internet', false);
    }

    public function test_the_nit_is_only_shown_when_it_is_configured(): void
    {
        $this->get(route('legal.privacy'))->assertDontSee('NIT:', false);

        config(['legal.controller.nit' => '900.123.456-7']);

        $this->get(route('legal.privacy'))->assertSee('900.123.456-7', false);
    }

    public function test_the_landing_page_links_to_every_legal_document_and_shows_the_cookie_notice(): void
    {
        $response = $this->get('/');

        $response->assertSee(route('legal.terms'), false);
        $response->assertSee(route('legal.privacy'), false);
        $response->assertSee(route('legal.cookies'), false);
        $response->assertSee(route('legal.refund'), false);
        $response->assertSee('id="cookieNotice"', false);
    }

    public function test_the_cookie_notice_is_an_animated_modal_except_on_the_cookie_policy_page(): void
    {
        $response = $this->get('/');

        $response->assertSee('role="dialog"', false);
        $response->assertSee('aria-modal="true"', false);
        $response->assertSee('@keyframes cookieDrop', false);
        $response->assertSee('Entendido', false);

        $this->get(route('legal.privacy'))->assertSee('id="cookieNotice"', false);
        $this->get(route('legal.cookies'))->assertDontSee('id="cookieNotice"', false);
    }
}
