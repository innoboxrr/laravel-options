<?php

namespace Innoboxrr\LaravelOptions\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Policies\OptionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Las politicas del paquete, declaradas a mano.
     *
     * Antes se descubrian recorriendo Policies/ y el nombre del modelo se
     * armaba mal (Models\Innoboxrr\...\Policies\Option), asi que OptionPolicy
     * nunca quedaba registrada. Solo funcionaba si Laravel adivinaba la
     * politica por convencion: una aplicacion que cambia esa convencion con
     * Gate::guessPolicyNamesUsing() se quedaba sin politica y toda escritura
     * respondia 403, tambien a un administrador.
     *
     * Tampoco se guarda en la cache: leerla al arrancar, con una clave que
     * comparten otros paquetes, mezclaba sus listas y rompia php artisan
     * migrate en una aplicacion nueva con CACHE_STORE=database.
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        Option::class => OptionPolicy::class,
    ];

    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
