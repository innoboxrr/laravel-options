<?php

namespace Innoboxrr\LaravelOptions\Console\Commands;

use Illuminate\Console\Command;
use Innoboxrr\LaravelOptions\Database\Seeders\OptionSeeder;

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
    protected $description = 'Crear las opciones por defecto que falten, sin tocar las existentes';

    /**
     * db:seed pide confirmacion en produccion, y llamado desde aqui no hay a
     * quien preguntar: se cancelaba y el comando decia igualmente que habia
     * sembrado. --force es seguro porque el seeder solo crea lo que falta.
     */
    public function handle(): int
    {
        $status = $this->call('db:seed', [
            '--class' => OptionSeeder::class,
            '--force' => true,
        ]);

        if ($status !== self::SUCCESS) {
            $this->error('No se pudieron sembrar las opciones.');

            return $status;
        }

        $this->info('OptionSeeder ejecutado correctamente.');

        return self::SUCCESS;
    }
}
