<?php

namespace Innoboxrr\LaravelOptions\Database\Seeders;

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
        Option::updateOrCreate([
            'key' => 'site_name',
        ],[
            'name' => 'Nombre del sitio',
            'value' => 'Mi Sitio',
        ]);

        Option::updateOrCreate([
            'key' => 'site_description',
        ],[
            'name' => 'Descripción del sitio',
            'value' => 'Descripción de mi sitio',
        ]);

        Option::updateOrCreate([
            'key' => 'theme',
        ],[
            'name' => 'App Config',
            'value' => json_encode([
                'home' => [
                    "title" => "Home",
                    "sections" => [
                        [
                            "theme" => "legacy",
                            "group" => "header",
                            "name" => "HeaderOne",
                            "props" => [
                                "display" => true,
                                "logo" => "https://via.placeholder.com/150",
                                "facebook" => "https://facebook.com",
                                "twitter" => "https://twitter.com",
                                "instagram" => "https://instagram.com",
                                "youtube" => "https://youtube.com",
                                "whatsapp" => "https://wa.me/1234567890",
                                "linkedin" => "https://linkedin.com",
                                "tiktok" => "https://tiktok.com",
                            ]
                        ]
                    ]
                ]
            ]),
        ]);
    }
}
