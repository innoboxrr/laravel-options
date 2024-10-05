<?php

namespace Innoboxrr\LaravelOptions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptionSeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'options:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecutar el seeder de opciones para el paquete LaravelOptions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Ejecutar el seeder específico del paquete
        Artisan::call('db:seed', [
            '--class' => 'Innoboxrr\\LaravelOptions\\Database\\Seeder\\OptionSeeder'
        ]);

        $this->info('OptionSeeder ejecutado correctamente.');

        return 0;
    }
}
