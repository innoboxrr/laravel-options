<?php

namespace Innoboxrr\LaravelOptions\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Un usuario de una aplicacion que no sigue la convencion del ecosistema: no
 * es App\Models\User y no define isAdmin().
 */
class PlainUser extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = [];
}
