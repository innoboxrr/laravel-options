<?php

namespace Innoboxrr\LaravelOptions\Database\Seeder;

use Illuminate\Database\Seeder;
use Innoboxrr\LaravelOptions\Models\Option;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lógica para crear opciones de ejemplo
        Option::create([
            'key' => 'site_name',
            'value' => 'Mi Sitio',
        ]);

        Option::create([
            'key' => 'site_description',
            'value' => 'Descripción de mi sitio',
        ]);
    }
}
