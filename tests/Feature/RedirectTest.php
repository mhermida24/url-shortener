<?php

namespace Tests\Feature;

use App\Models\Click;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirige_a_la_url_original(): void
    {
        $link = Link::factory()->create([
            'code' => 'abc123',
            'original_url' => 'https://laravel.com/docs',
        ]);

        $response = $this->get('/abc123');

        $response->assertRedirect('https://laravel.com/docs');
    }

    public function test_registra_un_clic_con_datos_del_visitante(): void
    {
        $link = Link::factory()->create(['code' => 'abc123']);

        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Referer' => 'https://www.google.com/',
        ])->get('/abc123');

        $this->assertDatabaseCount('clicks', 1);
        $this->assertDatabaseHas('clicks', [
            'link_id' => $link->id,
            'browser' => 'Chrome',
            'os' => 'Windows',
            'device' => 'desktop',
            'referer' => 'https://www.google.com/',
        ]);
    }

    public function test_codigo_inexistente_devuelve_404(): void
    {
        $this->get('/noexiste')->assertNotFound();
    }

    public function test_enlace_expirado_devuelve_410_y_no_registra_clic(): void
    {
        Link::factory()->create([
            'code' => 'vencido',
            'expires_at' => now()->subDay(),
        ]);

        $this->get('/vencido')->assertStatus(410);

        $this->assertDatabaseCount('clicks', 0);
    }

    public function test_enlace_con_limite_de_clics_devuelve_410_al_superarlo(): void
    {
        $link = Link::factory()->create([
            'code' => 'limite',
            'max_clicks' => 2,
        ]);

        $this->get('/limite')->assertRedirect();
        $this->get('/limite')->assertRedirect();
        $this->get('/limite')->assertStatus(410);

        $this->assertSame(2, Click::where('link_id', $link->id)->count());
    }
}