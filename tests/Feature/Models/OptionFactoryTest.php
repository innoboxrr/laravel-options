<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Models;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;

class OptionFactoryTest extends TestCase
{
    public function test_crea_opciones_con_clave_unica_nombre_y_valor(): void
    {
        $options = Option::factory()->count(25)->create();

        $this->assertSame(25, Option::count());
        $this->assertSame(25, $options->pluck('key')->unique()->count());

        foreach ($options as $option) {
            $this->assertMatchesRegularExpression('/^[a-z0-9_]+$/', $option->key);
            $this->assertNotEmpty($option->name);
            $this->assertNotEmpty($option->value);
        }
    }

    public function test_lo_que_genera_pasa_la_validacion_de_create(): void
    {
        $this->signIn(admin: true);

        $payload = Option::factory()->make()->only(['key', 'name', 'value']);

        $this->postJson(route('api.laravel-options.option.create'), $payload)
            ->assertCreated()
            ->assertJsonPath('data.key', $payload['key']);
    }
}
