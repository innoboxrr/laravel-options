<?php

namespace Innoboxrr\LaravelOptions\Database\Seeders;

use Illuminate\Database\Seeder;
use Innoboxrr\LaravelOptions\Models\Option;

class OptionSeeder extends Seeder
{
    /**
     * Crea las opciones que falten y no toca las que ya existen.
     *
     * Se ejecuta en cada despliegue: lo que el administrador cambio desde el
     * panel no se puede pisar con el valor por defecto. Con updateOrCreate se
     * pisaba.
     *
     * withTrashed() porque key es unica tambien entre las borradas: una
     * opcion que el administrador borro no se revive, y tampoco hace fallar
     * el seeder al intentar crearla otra vez.
     */
    public function run(): void
    {
        foreach ($this->defaults() as $key => $attributes) {
            Option::withTrashed()->firstOrCreate(['key' => $key], $attributes);
        }
    }

    /**
     * @return array<string, array{name: string, value: string}>
     */
    protected function defaults(): array
    {
        return [
            'site_name' => [
                'name' => 'Nombre del sitio',
                'value' => 'Mi Sitio',
            ],
            'site_description' => [
                'name' => 'Descripción del sitio',
                'value' => 'Descripción de mi sitio',
            ],
            'theme' => [
                'name' => 'App Config',
                'value' => json_encode([
                    'home' => [
                        'title' => 'Home',
                        'sections' => [
                            [
                                'theme' => 'legacy',
                                'group' => 'header',
                                'name' => 'HeaderOne',
                                'props' => [
                                    'display' => true,
                                    'logo' => 'https://i.imgur.com/WxNkK7J.png',
                                    'facebook' => 'https://facebook.com',
                                    'twitter' => 'https://twitter.com',
                                    'instagram' => 'https://instagram.com',
                                    'youtube' => 'https://youtube.com',
                                    'whatsapp' => 'https://wa.me/1234567890',
                                    'linkedin' => 'https://linkedin.com',
                                    'tiktok' => 'https://tiktok.com',
                                ],
                            ],
                        ],
                    ],
                ]),
            ],
        ];
    }
}
