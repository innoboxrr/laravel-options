<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Models;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;

/**
 * Lo que el panel puede mandar al crear y actualizar opciones.
 */
class OptionValidationTest extends TestCase
{
    private function option(): Option
    {
        return Option::forceCreate(['key' => 'theme', 'name' => 'App Config', 'value' => '{}']);
    }

    public function test_crear_con_un_valor_estructurado_lo_guarda_como_json(): void
    {
        $this->signIn(admin: true);

        $theme = ['home' => ['title' => 'Inicio', 'sections' => [['name' => 'HeaderOne', 'props' => ['display' => true]]]]];

        $this->postJson(route('api.laravel-options.option.create'), ['key' => 'theme', 'name' => 'App Config', 'value' => $theme])
            ->assertCreated();

        $this->assertSame($theme, json_decode(Option::where('key', 'theme')->value('value'), true));
    }

    public function test_actualizar_con_un_valor_estructurado_lo_guarda_como_json(): void
    {
        $this->signIn(admin: true);

        $option = $this->option();

        $this->putJson(route('api.laravel-options.option.update'), ['option_id' => $option->id, 'value' => ['home' => ['title' => 'Inicio']]])
            ->assertOk();

        $this->assertSame(['home' => ['title' => 'Inicio']], json_decode($option->fresh()->value, true));
    }

    public function test_se_puede_crear_una_opcion_sin_valor(): void
    {
        $this->signIn(admin: true);

        $this->postJson(route('api.laravel-options.option.create'), ['key' => 'announcement', 'name' => 'Aviso', 'value' => ''])
            ->assertCreated();

        $this->assertNull(Option::where('key', 'announcement')->value('value'));
    }

    public function test_actualizar_con_la_clave_nula_es_un_error_de_validacion_y_no_un_500(): void
    {
        $this->signIn(admin: true);

        $option = $this->option();

        $this->putJson(route('api.laravel-options.option.update'), ['option_id' => $option->id, 'key' => null])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('key');

        $this->assertSame('theme', $option->fresh()->key);
    }

    public function test_actualizar_sin_mandar_la_clave_la_conserva(): void
    {
        $this->signIn(admin: true);

        $option = $this->option();

        $this->putJson(route('api.laravel-options.option.update'), ['option_id' => $option->id, 'name' => 'Tema'])
            ->assertOk();

        $this->assertSame('theme', $option->fresh()->key);
        $this->assertSame('Tema', $option->fresh()->name);
    }

    public function test_la_clave_sigue_siendo_unica_al_crear_y_al_actualizar(): void
    {
        $this->signIn(admin: true);

        $option = $this->option();
        $other = Option::forceCreate(['key' => 'site_name', 'name' => 'Nombre', 'value' => 'Mi Sitio']);

        $this->postJson(route('api.laravel-options.option.create'), ['key' => 'theme', 'name' => 'Otro', 'value' => 'x'])
            ->assertJsonValidationErrors('key');

        $this->putJson(route('api.laravel-options.option.update'), ['option_id' => $other->id, 'key' => 'theme'])
            ->assertJsonValidationErrors('key');

        $this->putJson(route('api.laravel-options.option.update'), ['option_id' => $option->id, 'key' => 'theme', 'value' => 'y'])
            ->assertOk();
    }
}
