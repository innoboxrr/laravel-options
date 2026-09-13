<?php

namespace Innoboxrr\LaravelOptions\Database\Factories;

/*
 * Docs: https://fakerphp.github.io/
 */

use Innoboxrr\LaravelOptions\Models\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{

    protected $model = Option::class;

    /**
     * Una opcion que la tabla acepta y que pasa la validacion de create:
     * la clave es unica en la tabla y va en snake_case, como las del seeder.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => str_replace('-', '_', $this->faker->unique()->slug(3)),
            'name' => $this->faker->sentence(3),
            'value' => $this->faker->sentence(),
        ];
    }

}
