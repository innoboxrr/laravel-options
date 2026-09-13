<?php

namespace Innoboxrr\LaravelOptions\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * El usuario de la aplicacion anfitriona, para los tests.
 *
 * El paquete no conoce el modelo de quien lo instala. Este tiene lo que el
 * ecosistema espera de el: autenticarse, recibir notificaciones y decir con
 * isAdmin() si administra.
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $guarded = [];

    public function isAdmin(): bool
    {
        return false;
    }
}
