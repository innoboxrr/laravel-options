<?php

namespace Innoboxrr\LaravelOptions\Tests\Fixtures;

class AdminUser extends User
{
    public function isAdmin(): bool
    {
        return true;
    }
}
