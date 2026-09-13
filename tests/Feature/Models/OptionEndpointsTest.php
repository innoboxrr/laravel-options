<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Models;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;

/**
 * Que la API de opciones responde como la usan el sitio y el panel.
 *
 * El sitio publico lee las opciones sin sesion, asi que index y show son
 * publicos; todo lo que escribe pide sesion y lo decide OptionPolicy.
 */
class OptionEndpointsTest extends TestCase
{
    private function option(array $attributes = []): Option
    {
        return Option::forceCreate($attributes + [
            'key' => uniqid('key_'),
            'name' => 'Nombre',
            'value' => 'Valor',
        ]);
    }

    public function test_un_invitado_lee_el_indice(): void
    {
        $option = $this->option(['key' => 'site_name', 'value' => 'Mi Sitio']);

        $this->getJson(route('api.laravel-options.option.index'))
            ->assertOk()
            ->assertJsonFragment(['id' => $option->id, 'key' => 'site_name', 'value' => 'Mi Sitio']);
    }

    public function test_un_invitado_lee_una_opcion(): void
    {
        $option = $this->option();

        $this->getJson(route('api.laravel-options.option.show', ['option_id' => $option->id]))
            ->assertOk()
            ->assertJsonPath('data.id', $option->id)
            ->assertJsonPath('data.key', $option->key);
    }

    /**
     * search-surge pagina de 10 en 10 si no se le dice nada, y el sitio carga
     * todas las opciones de una vez: paginate=0 las trae todas.
     */
    public function test_paginate_cero_devuelve_todas_las_opciones(): void
    {
        foreach (range(1, 15) as $i) {
            $this->option(['key' => "key_{$i}"]);
        }

        $this->getJson(route('api.laravel-options.option.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data');

        $this->getJson(route('api.laravel-options.option.index', ['paginate' => 0]))
            ->assertOk()
            ->assertJsonCount(15, 'data');
    }

    public function test_un_invitado_no_puede_escribir(): void
    {
        $option = $this->option();
        $id = ['option_id' => $option->id];

        $this->postJson(route('api.laravel-options.option.create'), ['key' => 'k', 'name' => 'n', 'value' => 'v'])->assertUnauthorized();
        $this->putJson(route('api.laravel-options.option.update'), $id + ['value' => 'otro'])->assertUnauthorized();
        $this->deleteJson(route('api.laravel-options.option.delete'), $id)->assertUnauthorized();
        $this->postJson(route('api.laravel-options.option.restore'), $id)->assertUnauthorized();
        $this->deleteJson(route('api.laravel-options.option.force.delete'), $id)->assertUnauthorized();
        $this->postJson(route('api.laravel-options.option.export'))->assertUnauthorized();
        $this->getJson(route('api.laravel-options.option.policies'))->assertUnauthorized();
        $this->getJson(route('api.laravel-options.option.policy', ['policy' => 'create']))->assertUnauthorized();

        $this->assertSame('Valor', $option->fresh()->value);
        $this->assertSame(1, Option::count());
    }
}
