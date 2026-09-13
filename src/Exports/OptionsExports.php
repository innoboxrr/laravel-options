<?php

namespace Innoboxrr\LaravelOptions\Exports;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\SearchSurge\Search\Builder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OptionsExports implements FromView
{

    public function __construct(protected array $data)
    {
    }

    public function view(): View
    {
        return view($this->viewName(), [
            'options' => $this->getQuery(),
            'exportCols' => Option::$export_cols,
        ]);
    }

    public function getQuery()
    {

        $builder = new Builder();

        // lazy() en vez de get(): un export recorre la tabla entera y
        // hidratar todas las filas a la vez es lo que revienta la memoria.
        return $builder->lazy(Option::class, $this->data);

    }

    /**
     * La configuracion se fusiona como laravel-options y las vistas se cargan
     * como laravel-options::, pero aqui se leia innoboxrrlaraveloptions.*: la
     * vista nunca se encontraba. Una configuracion publicada con una version
     * anterior todavia trae ese prefijo, asi que si la vista configurada no
     * existe se usa la del paquete.
     */
    protected function viewName(): string
    {
        $view = config('laravel-options.excel_view', 'laravel-options::excel.') . 'option';

        return view()->exists($view) ? $view : 'laravel-options::excel.option';
    }

}
