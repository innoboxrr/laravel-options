<?php

namespace Innoboxrr\LaravelOptions\Policies;

use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable as User;
use Innoboxrr\LaravelOptions\Models\Option;

class OptionPolicy
{

    /**
     * El usuario es el de la aplicacion que instala el paquete, sea cual sea
     * su clase: por eso el contrato y no App\Models\User. Con App\Models\User
     * cualquier otra clase de usuario provocaba un TypeError, y quien no
     * administraba recibia un 500 en vez de un 403.
     *
     * En el ecosistema quien administra lo dice isAdmin(). Un usuario que no
     * lo define no administra, y deciden los metodos de abajo.
     */
    public function before(User $user, string $ability): ?bool
    {

        // Lo que ni un administrador hace sin que lo decidas en su metodo. El
        // borrado permanente nace apagado: quitalo de aqui y escribe quien
        // puede en forceDelete() cuando lo quieras.
        $exceptAbilities = ['forceDelete'];

        $isAdmin = method_exists($user, 'isAdmin') && $user->isAdmin();

        if ($isAdmin && ! in_array($ability, $exceptAbilities, true)) {

            return true;

        }

        return null;

    }

    public function index(User $user): Response|bool
    {
        return false;
    }

    public function viewAny(User $user): Response|bool
    {
        return false;
    }

    public function view(User $user, Option $option): Response|bool
    {
        return false;
    }

    public function create(User $user): Response|bool
    {
        return false;
    }

    public function update(User $user, Option $option): Response|bool
    {
        return false;
    }

    public function delete(User $user, Option $option): Response|bool
    {
        return false;
    }

    public function restore(User $user, Option $option): Response|bool
    {
        return false;
    }

    public function forceDelete(User $user, Option $option): Response|bool
    {
        return false;
    }

    public function export(User $user): Response|bool
    {
        return false;
    }

}
