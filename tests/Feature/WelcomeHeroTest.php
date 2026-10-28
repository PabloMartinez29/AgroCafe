<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomeHeroTest extends TestCase
{
    public function test_the_welcome_hero_uses_the_new_video_and_layout(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('hero.video'), false);
        $response->assertDontSee('Explora el café', false);
        $response->assertDontSee('como nunca antes.', false);
        $response->assertSee('data-phrase="El arte del café, nacido en nuestra tierra."', false);
        $response->assertSee('La esencia del café colombiano, sin límites.', false);
        $response->assertSee('Conectamos el campo con nuevas oportunidades para llevar el valor de cada cosecha más lejos.', false);
        $response->assertSee('Tradición que inspira.', false);
        $response->assertSee('Calidad que trasciende.', false);
        $response->assertSee('data-animation="rise"', false);
        $response->assertSee('data-animation="slide"', false);
        $response->assertSee('data-animation="wipe"', false);
        $response->assertSee('@keyframes heroRise', false);
        $response->assertSee('@keyframes heroSlideIn', false);
        $response->assertSee('@keyframes heroWipe', false);
        $response->assertSee('data-phrase-slide="0"', false);
        $response->assertSee('data-phrase-slide="1"', false);
        $response->assertSee('data-phrase-slide="2"', false);
        $response->assertDontSee('Compras, inventario y ventas en un solo lugar', false);
        $response->assertDontSee('De la finca', false);
        $response->assertDontSee('data-beat', false);
        $response->assertSee('data-reveal', false);
        $response->assertSee('id="heroStage"', false);
        $response->assertSee('id="heroVideo"', false);
        $response->assertSee('id="heroImageOne"', false);
        $response->assertSee('id="heroImageTwo"', false);
        $response->assertSee('images/hero/1.svg', false);
        $response->assertSee('images/hero/2.svg?v=3', false);
        $response->assertDontSee('images/hero/finca.jpg', false);
        $response->assertDontSee('images/hero/granos.jpg', false);
        $response->assertSee('data-slide="0"', false);
        $response->assertSee('data-slide="1"', false);
        $response->assertSee('data-slide="2"', false);
        $response->assertSee('hero-progress-track is-current', false);
        $response->assertSee('flex-grow: 3', false);
        $response->assertDontSee('scaleY', false);
        $response->assertSee('id="heroPreviewFrame"', false);
        $response->assertSee('id="heroPreviewOne"', false);
        $response->assertSee('id="heroPreviewTwo"', false);
        $response->assertSee('hero-thumb', false);
        $response->assertSee('data-cycle="image,video,image"', false);
        $response->assertSee("addEventListener('ended'", false);
        $response->assertDontSee('h-[320vh]', false);
        $response->assertDontSee('scrollProgress', false);
        $response->assertSee('id="heroPreview"', false);
        $response->assertSee('loop', false);
        $response->assertSee('id="heroBrand"', false);
        $response->assertSee('const firstImageHoldMs = 3000;', false);
        $response->assertSee('holdFor(currentSlide)', false);
        $response->assertSee('id="heroStage" class="sticky top-0 z-0', false);
        $response->assertSee('id="pageContent" class="relative z-10', false);
        $response->assertSee('Regístrate', false);
        $response->assertDontSee('id="navbar"', false);
        $response->assertSee('id="heroMarkAgro"', false);
        $response->assertSee('>AGRO</p>', false);
        $response->assertSee('id="heroMarkCafe"', false);
        $response->assertSee('>CAFÉ</p>', false);
        $response->assertSee('id="heroPlantsOne"', false);
        $response->assertSee('id="heroPlantsTwo"', false);
        $response->assertDontSee('id="heroMark"', false);
        $response->assertDontSee('id="heroPlants"', false);
        $response->assertDontSee('data-fade-end', false);
        $response->assertDontSee('updateHeroMark', false);
        $response->assertDontSee('coffee-hero.mp4', false);
        $response->assertSee('Consejos para mejorar la cosecha y las ventas', false);
        $response->assertSee('¿Cómo almacenar el café para proteger su calidad antes de venderlo?', false);
        $response->assertSee('Recomendaciones prácticas sobre recolección, beneficio, secado, almacenamiento y calidad del café.', false);
        $response->assertDontSee('Módulos Principales', false);
        $response->assertDontSee('Gestión de Compras', false);
        $response->assertDontSee('typing-text', false);
    }

    public function test_the_current_prices_section_has_scroll_animations(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="acerca-de" class="price-stage', false);
        $response->assertSee('data-price-stage', false);
        $response->assertSee('price-title', false);
        $response->assertSee('price-bar', false);
        $response->assertSee('price-line', false);
        $response->assertSee('price-panel', false);
        $response->assertSee('price-row', false);
        $response->assertSee('price-photo', false);
        $response->assertSee('price-value', false);
        $response->assertSee('data-parallax', false);
        $response->assertSee('paintScrub', false);
        $response->assertSee("addEventListener('scroll', requestScrub", false);
        $response->assertSee("['.price-panel', 'right']", false);
        $response->assertSee("'footer .grid > *'", false);
        $response->assertDontSee('@keyframes pricePanelIn', false);
        $response->assertDontSee('IntersectionObserver', false);
        $response->assertSee('countUpPrice', false);
        $response->assertSee('prefers-reduced-motion: reduce', false);
        $response->assertSee('Lista de precios', false);
    }

    public function test_the_tips_section_scrolls_horizontally_while_scrolling_down(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="consejos"', false);
        $response->assertSee('data-hscroll', false);
        $response->assertSee('hscroll-sticky', false);
        $response->assertSee('hscroll-viewport', false);
        $response->assertSee('hscroll-track', false);
        $response->assertSee('hscroll-progress', false);
        $response->assertSee('paintHscroll', false);
        $response->assertSee('window.hscrollScrollFor', false);
        $response->assertSee('id="recoleccion"', false);
        $response->assertSee('id="venta"', false);
        $response->assertDontSee('transform hover:-translate-y-2" data-reveal', false);
        $response->assertSee('section-title-clip', false);
        $response->assertSee('section-title text-4xl', false);
        $response->assertSee('section-bar w-24', false);
        $response->assertSee('section-line text-xl', false);
        $response->assertSee("['.section-title', 'title']", false);
        $response->assertSee("['.section-bar', 'bar']", false);
        $response->assertSee("['.section-line', 'rise']", false);
    }

    public function test_the_platform_section_explains_what_agrocafe_is(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="plataforma"', false);
        $response->assertSee('Acerca de la plataforma', false);
        $response->assertSee('¿Qué es AgroCafé?', false);
        $response->assertSee('¿Con qué fin se hizo?', false);
        $response->assertSee('¿Qué impacto tiene?', false);
        $response->assertSee('Cuenta con 10 módulos', false);
        $response->assertSee('soporte de lunes a viernes', false);
        $response->assertSee('mailto:contacto@agrocafe.com', false);
        $response->assertDontSee('data-count-to', false);
        $response->assertDontSee('Los 10 módulos', false);
        $response->assertDontSee('id="soporte"', false);
        $response->assertDontSee('Tiene soporte', false);
        $response->assertDontSee('fa-headset', false);
        $response->assertDontSee('Características Destacadas', false);
        $response->assertDontSee('id="caracteristicas"', false);
        $response->assertDontSee('Aplicación Móvil', false);
        $response->assertDontSee('currentSlide = (currentSlide + 1)', false);
    }

    public function test_the_footer_only_shows_the_brand_and_contact_columns(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeInOrder(['<footer', 'AgroCafé', 'Contacto', 'contacto@agrocafe.com', '+57 300 123 4567'], false);
        $response->assertDontSee('Enlaces Rápidos', false);
        $response->assertDontSee('Iniciar Sesión', false);
        $response->assertDontSee('Acerca de la plataforma</a>', false);
    }

    public function test_the_hero_video_can_be_seeked_by_byte_range(): void
    {
        $videoPath = $this->heroVideoPath();
        $createdTemporaryVideo = false;

        if ($videoPath === null) {
            $videoPath = public_path('video/gemini_generated_video_079d102d.mp4');

            if (! is_dir(dirname($videoPath))) {
                mkdir(dirname($videoPath), 0755, true);
            }

            file_put_contents($videoPath, str_repeat('0', 1024));
            $createdTemporaryVideo = true;
        }

        try {
            $response = $this->withHeaders([
                'Range' => 'bytes=0-99',
            ])->get(route('hero.video'));

            $size = filesize($videoPath);

            $response->assertStatus(206);
            $response->assertHeader('Accept-Ranges', 'bytes');
            $response->assertHeader('Content-Range', 'bytes 0-99/'.$size);
        } finally {
            if ($createdTemporaryVideo) {
                unlink($videoPath);
            }
        }
    }

    public function test_the_hero_video_returns_not_found_when_the_file_is_missing(): void
    {
        if ($this->heroVideoPath() !== null) {
            $this->markTestSkipped('The hero video exists locally.');
        }

        $this->get(route('hero.video'))->assertNotFound();
    }

    private function heroVideoPath(): ?string
    {
        foreach ([
            public_path('video/gemini_generated_video_079d102d.mp4'),
            storage_path('app/videos/gemini_generated_video_079d102d.mp4'),
        ] as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}
