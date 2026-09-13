<?php

namespace Innoboxrr\LaravelOptions\Tests\Feature\Authorization;

use Illuminate\Support\Facades\Gate;
use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Policies\OptionPolicy;
use Innoboxrr\LaravelOptions\Tests\TestCase;

class PolicyRegistrationTest extends TestCase
{
    /**
     * La aplicacion puede cambiar como adivina Laravel las politicas; la del
     * paquete no puede depender de esa convencion.
     */
    public function test_option_policy_se_registra_aunque_la_aplicacion_cambie_la_convencion(): void
    {
        Gate::guessPolicyNamesUsing(fn (string $class) => ['App\\Policies\\' . class_basename($class) . 'Policy']);

        $this->assertInstanceOf(OptionPolicy::class, Gate::getPolicyFor(Option::class));
    }

    public function test_un_administrador_crea_aunque_la_aplicacion_cambie_la_convencion(): void
    {
        Gate::guessPolicyNamesUsing(fn () => []);

        $this->signIn(admin: true);

        $this->postJson(route('api.laravel-options.option.create'), ['key' => 'site_name', 'name' => 'Nombre', 'value' => 'Mi Sitio'])
            ->assertCreated();
    }
}
