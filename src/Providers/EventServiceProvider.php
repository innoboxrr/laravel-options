<?php

namespace Innoboxrr\LaravelOptions\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Innoboxrr\LaravelOptions\Http\Events\Option\Events;
use Innoboxrr\LaravelOptions\Http\Events\Option\Listeners;
use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\LaravelOptions\Observers\OptionObserver;

/**
 * Enlaza los eventos del paquete con sus listeners y el observer de Option.
 *
 * Extiende el ServiceProvider base y no el EventServiceProvider de Laravel:
 * ese registra SendEmailVerificationNotification para Registered en cada
 * subclase, y la aplicacion que lo instalaba mandaba el correo de
 * verificacion una vez por cada paquete asi.
 *
 * Tampoco pasa por la cache. Antes descubria eventos y observers recorriendo
 * carpetas y guardaba el resultado con Cache::remember() al arrancar: con
 * CACHE_STORE=database, el valor por defecto de Laravel, eso consulta la
 * tabla cache antes de que php artisan migrate haya podido crearla.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected array $listen = [
        Events\CreateEvent::class => [
            Listeners\CreateEvent\DefaultOperation::class,
        ],
        Events\UpdateEvent::class => [
            Listeners\UpdateEvent\DefaultOperation::class,
        ],
        Events\DeleteEvent::class => [
            Listeners\DeleteEvent\DefaultOperation::class,
        ],
        Events\RestoreEvent::class => [
            Listeners\RestoreEvent\DefaultOperation::class,
        ],
        Events\ForceDeleteEvent::class => [
            Listeners\ForceDeleteEvent\DefaultOperation::class,
        ],
        Events\ExportEvent::class => [
            Listeners\ExportEvent\DefaultOperation::class,
            Listeners\ExportEvent\SendExportNotification::class,
        ],
    ];

    public function boot(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        Option::observe(OptionObserver::class);
    }
}
