<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Authorization;

use Illuminate\Support\Facades\Gate;
use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\TestCase;

/**
 * El borrado permanente lo decide OptionPolicy: apagado por defecto, también
 * para un administrador, y encendido cuando la aplicación lo concede.
 */
class ForceDeleteTest extends TestCase
{
    private function option(): Option
    {
        return Option::forceCreate(['key' => uniqid('key_'), 'name' => 'Nombre', 'value' => 'Valor']);
    }

    public function test_un_administrador_no_borra_para_siempre_por_defecto(): void
    {
        $this->signIn(admin: true);

        $option = $this->option();

        $this->deleteJson(route('api.laravel-options.option.force.delete'), ['option_id' => $option->id])
            ->assertForbidden();

        $this->assertNotNull(Option::withTrashed()->find($option->id));
    }

    /**
     * Lo que el panel pregunta a la API de políticas tiene que coincidir con
     * lo que después hace la ruta.
     */
    public function test_la_api_de_politicas_dice_lo_mismo_que_la_ruta(): void
    {
        $this->signIn(admin: true);

        $option = $this->option();

        $this->getJson(route('api.laravel-options.option.policy', ['policy' => 'forceDelete', 'id' => $option->id]))
            ->assertOk()
            ->assertExactJson(['forceDelete' => false]);

        $this->getJson(route('api.laravel-options.option.policy', ['policy' => 'delete', 'id' => $option->id]))
            ->assertOk()
            ->assertExactJson(['delete' => true]);
    }

    public function test_cuando_la_aplicacion_lo_concede_borra_para_siempre(): void
    {
        Gate::before(fn ($user, string $ability) => $ability === 'forceDelete' ? true : null);

        $this->signIn();

        $option = $this->option();
        $option->delete();

        $this->deleteJson(route('api.laravel-options.option.force.delete'), ['option_id' => $option->id])
            ->assertOk();

        $this->assertNull(Option::withTrashed()->find($option->id));
    }
}
