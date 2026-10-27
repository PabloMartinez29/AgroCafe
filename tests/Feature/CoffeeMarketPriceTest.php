<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CoffeeMarketPriceTest extends TestCase
{
    public function test_the_about_section_shows_real_coffee_prices_by_type(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Precios actuales', false);
        $response->assertSee('El precio del café no es uno solo.', false);
        $response->assertSee('Café verde', false);
        $response->assertSee('Café seco', false);
        $response->assertSee('Lista de precios', false);
        $response->assertSee('images/cafe/verde.svg', false);
        $response->assertSee('images/cafe/seco.svg', false);
        $response->assertSee('images/cafe/pasilla.svg', false);
        $response->assertSee('data-cafe-foto="verde"', false);
        $response->assertSee('data-cafe-foto="seco"', false);
        $response->assertSee('data-cafe-foto="pasilla"', false);
        $response->assertSee('rounded-full', false);
        $response->assertDontSee('Acerca de AgroCafé', false);
        $response->assertDontSee('AgroCafé se creó para saber los precios reales', false);
        $response->assertSee('data-market-price="verde"', false);
        $response->assertSee('data-market-price="seco"', false);
        $response->assertSee('data-market-price="pasilla"', false);
        $response->assertSee('id="precios-mercado"', false);
        $response->assertSee('/precios-mercado', false);
        $response->assertSee('refreshMarketPrices', false);
        $response->assertDontSee('Misión', false);
        $response->assertDontSee('Visión', false);
        $response->assertDontSee('Sistema integral diseñado para transformar la gestión del negocio cafetero', false);
        $response->assertDontSee('Integrable', false);
    }

    public function test_market_prices_match_the_official_colombia_bulletin(): void
    {
        $this->fakeMarket('2.065.000', '2026-10-27');

        $response = $this->getJson(route('coffee-market.prices'));

        $response->assertOk();
        $response->assertJsonPath('source', 'Federación Nacional de Cafeteros de Colombia');
        $response->assertJsonPath('types.0.key', 'verde');
        $response->assertJsonPath('types.0.price', 21507);
        $response->assertJsonPath('types.0.unit', 'kg');
        $response->assertJsonPath('types.0.date', '2026-10-26');
        $response->assertJsonPath('types.1.key', 'seco');
        $response->assertJsonPath('types.1.price', 2065000);
        $response->assertJsonPath('types.1.price_per_kg', 16520);
        $response->assertJsonPath('types.1.unit', 'carga');
        $response->assertJsonPath('types.1.date', '2026-10-27');
        $response->assertJsonPath('types.2.key', 'pasilla');
        $response->assertJsonPath('types.2.price', 12000);
        $response->assertJsonPath('types.2.date', '2026-10-26');
    }

    public function test_market_prices_update_when_the_official_price_changes(): void
    {
        $carga = '2.065.000';
        $date = '2026-10-27';
        $pdf = $this->pdfWithText($this->bulletinText());

        Http::fake(function (Request $request) use (&$carga, &$date, $pdf) {
            if (str_contains($request->url(), 'precio_cafe.pdf')) {
                return Http::response($pdf, 200, ['Content-Type' => 'application/pdf']);
            }

            $html = '<span>Precio interno de referencia: $'.$carga.'</span>'
                .'<div>Fecha: '.$date.'</div>'
                .'<a href="https://federaciondecafeteros.org/wp-content/uploads/2026/03/precio_cafe.pdf">boletin</a>';

            return Http::response($html, 200);
        });

        $this->getJson(route('coffee-market.prices'))->assertJsonPath('types.1.price', 2065000);

        $carga = '2.080.000';
        $date = '2026-10-28';

        $this->getJson(route('coffee-market.prices'))->assertJsonPath('types.1.price', 2065000);

        Cache::flush();

        $this->getJson(route('coffee-market.prices'))
            ->assertOk()
            ->assertJsonPath('types.1.price', 2080000)
            ->assertJsonPath('types.1.price_per_kg', 16640)
            ->assertJsonPath('types.1.date', '2026-10-28');
    }

    public function test_market_prices_use_fallback_when_the_source_is_unavailable(): void
    {
        Cache::flush();

        Http::fake([
            '*' => Http::response('error', 500),
        ]);

        $this->getJson(route('coffee-market.prices'))
            ->assertOk()
            ->assertJsonPath('types.0.key', 'verde')
            ->assertJsonPath('types.0.price', 21507)
            ->assertJsonPath('types.1.key', 'seco')
            ->assertJsonPath('types.1.price', 2100000)
            ->assertJsonPath('types.2.key', 'pasilla')
            ->assertJsonPath('types.2.price', 12000)
            ->assertJsonPath('note', 'Mostramos el último precio de referencia. El mercado en vivo no respondió a tiempo.');
    }

    private function fakeMarket(string $carga, string $date): void
    {
        $pdf = $this->pdfWithText($this->bulletinText());

        Http::fake(function (Request $request) use ($carga, $date, $pdf) {
            if (str_contains($request->url(), 'precio_cafe.pdf')) {
                return Http::response($pdf, 200, ['Content-Type' => 'application/pdf']);
            }

            $html = '<span>Precio interno de referencia: $'.$carga.'</span>'
                .'<div>Fecha: '.$date.'</div>'
                .'<a href="https://federaciondecafeteros.org/wp-content/uploads/2026/03/precio_cafe.pdf">boletin</a>';

            return Http::response($html, 200);
        });
    }

    private function bulletinText(): string
    {
        return <<<'TEXT'
Octubre 26 / 2026
Precio total por carga de 125
Kg de pergamino seco FR 94
       2,100,000
COP
Precio total  de pasilla
contenida en el pergamino
     12,000
COP/Kg
94
93.09
8.16
2,002,080
97,920
          2,100,000
TEXT;
    }

    private function pdfWithText(string $text): string
    {
        $stream = 'BT <'.strtoupper(bin2hex($text)).'> Tj ET';
        $length = strlen($stream);

        return "%PDF-1.3\n"
            ."1 0 obj\n<< /Length 2 0 R >>\nstream\n{$stream}\nendstream\nendobj\n"
            ."2 0 obj\n{$length}\nendobj\n";
    }
}
