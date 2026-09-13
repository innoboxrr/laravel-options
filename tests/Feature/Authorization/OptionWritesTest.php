<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Authorization;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Tests\Fixtures\PlainUser;
use Innoboxrr\LaravelOptions\Tests\TestCase;

/**
 * Quien escribe opciones: un administrador sí, cualquier otro usuario no.
 */
class OptionWritesTest extends TestCase
{
    private function option(array $attributes = []): Option
    {
        return Option::forceCreate($attributes + [
            'key' => uniqid('key_'),
            'name' => 'Nombre',
            'value' => 'Valor',
        ]);
    }

    public function test_un_administrador_crea_actualiza_borra_y_restaura(): void
    {
        $this->signIn(admin: true);

        $id = $this->postJson(route('api.laravel-options.option.create'), [
            'key' => 'site_name',
            'name' => 'Nombre del sitio',
            'value' => 'Mi Sitio',
        ])->assertCreated()->json('data.id');

        $this->assertSame('Mi Sitio', Option::findOrFail($id)->value);

        $this->putJson(route('api.laravel-options.option.update'), ['option_id' => $id, 'value' => 'Otro Sitio'])
            ->assertOk()
            ->assertJsonPath('data.value', 'Otro Sitio');

        $this->assertSame('Otro Sitio', Option::findOrFail($id)->value);

        $this->deleteJson(route('api.laravel-options.option.delete'), ['option_id' => $id])->assertOk();

        $this->assertNull(Option::find($id));
        $this->assertNotNull(Option::withTrashed()->find($id));

        $this->postJson(route('api.laravel-options.option.restore'), ['option_id' => $id])->assertOk();

        $this->assertNotNull(Option::find($id));
    }

    public function test_un_usuario_que_no_administra_recibe_403_al_escribir(): void
    {
        $this->signIn();

        $this->assertWritesAreForbidden();
    }

    /**
     * El usuario de la aplicación puede no ser App\Models\User ni tener
     * isAdmin(): la política lo trata como a quien no administra, sin romper.
     */
    public function test_un_usuario_de_otra_clase_y_sin_is_admin_recibe_403(): void
    {
        $user = PlainUser::forceCreate(['name' => 'Plain', 'email' => 'plain@example.test', 'password' => 'secret']);

        $this->actingAs($user, 'sanctum');

        $this->assertWritesAreForbidden();
    }

    private function assertWritesAreForbidden(): void
    {
        $option = $this->option();
        $trashed = $this->option();
        $trashed->delete();

        $this->postJson(route('api.laravel-options.option.create'), ['key' => 'nueva', 'name' => 'n', 'value' => 'v'])->assertForbidden();
        $this->putJson(route('api.laravel-options.option.update'), ['option_id' => $option->id, 'value' => 'otro'])->assertForbidden();
        $this->deleteJson(route('api.laravel-options.option.delete'), ['option_id' => $option->id])->assertForbidden();
        $this->postJson(route('api.laravel-options.option.restore'), ['option_id' => $trashed->id])->assertForbidden();
        $this->deleteJson(route('api.laravel-options.option.force.delete'), ['option_id' => $option->id])->assertForbidden();
        $this->postJson(route('api.laravel-options.option.export'))->assertForbidden();

        $this->assertSame('Valor', $option->fresh()->value);
        $this->assertFalse(Option::where('key', 'nueva')->exists());
        $this->assertTrue($trashed->fresh()->trashed());
    }
}
