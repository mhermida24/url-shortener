<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/links')->assertRedirect('/login');
    }

    public function test_usuario_puede_crear_un_enlace(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/links', [
            'original_url' => 'https://laravel.com/docs',
        ]);

        $response->assertRedirect(route('links.index'));
        $this->assertDatabaseHas('links', [
            'user_id' => $user->id,
            'original_url' => 'https://laravel.com/docs',
        ]);
    }

    public function test_se_genera_un_codigo_si_no_se_envia_uno(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/links', [
            'original_url' => 'https://laravel.com/docs',
        ]);

        $link = Link::first();

        $this->assertNotNull($link->code);
        $this->assertSame(6, strlen($link->code));
    }

    public function test_se_puede_usar_un_codigo_personalizado(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/links', [
            'original_url' => 'https://laravel.com/docs',
            'code' => 'mi-portfolio',
        ]);

        $this->assertDatabaseHas('links', ['code' => 'mi-portfolio']);
    }

    public function test_rechaza_una_url_invalida(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/links', [
            'original_url' => 'hola',
        ]);

        $response->assertSessionHasErrors('original_url');
        $this->assertDatabaseCount('links', 0);
    }

    public function test_rechaza_urls_con_protocolo_peligroso(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/links', [
            'original_url' => 'javascript:alert(1)',
        ]);

        $response->assertSessionHasErrors('original_url');
    }

    public function test_rechaza_codigos_reservados(): void
    {
        $user = User::factory()->create();

        foreach (['login', 'LOGIN', 'dashboard'] as $code) {
            $this->actingAs($user)->post('/links', [
                'original_url' => 'https://laravel.com/docs',
                'code' => $code,
            ])->assertSessionHasErrors('code');
        }

        $this->assertDatabaseCount('links', 0);
    }

    public function test_rechaza_codigos_duplicados_y_con_formato_invalido(): void
    {
        $user = User::factory()->create();
        Link::factory()->create(['code' => 'repetido']);

        $this->actingAs($user)->post('/links', [
            'original_url' => 'https://laravel.com/docs',
            'code' => 'repetido',
        ])->assertSessionHasErrors('code');

        $this->actingAs($user)->post('/links', [
            'original_url' => 'https://laravel.com/docs',
            'code' => 'con espacio',
        ])->assertSessionHasErrors('code');
    }

    public function test_un_usuario_no_puede_ver_ni_editar_enlaces_ajenos(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this->actingAs($intruder)->get("/links/{$link->id}")->assertForbidden();
        $this->actingAs($intruder)->get("/links/{$link->id}/edit")->assertForbidden();
        $this->actingAs($intruder)->put("/links/{$link->id}", [
            'original_url' => 'https://hack.com',
        ])->assertForbidden();
    }

    public function test_un_usuario_no_puede_borrar_enlaces_ajenos(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this->actingAs($intruder)->delete("/links/{$link->id}")->assertForbidden();

        $this->assertDatabaseHas('links', ['id' => $link->id]);
    }

    public function test_el_dueno_puede_ver_y_borrar_su_enlace(): void
    {
        $owner = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this->actingAs($owner)->get("/links/{$link->id}")->assertOk();
        $this->actingAs($owner)->delete("/links/{$link->id}")->assertRedirect(route('links.index'));

        $this->assertDatabaseMissing('links', ['id' => $link->id]);
    }

    public function test_el_listado_solo_muestra_enlaces_propios(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        Link::factory()->for($me)->create(['code' => 'miolink']);
        Link::factory()->for($other)->create(['code' => 'ajeno1']);

        $this->actingAs($me)->get('/links')
            ->assertSee('miolink')
            ->assertDontSee('ajeno1');
    }
}