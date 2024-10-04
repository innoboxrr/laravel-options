<?php

namespace Innoboxrr\LaravelOptions\Exports;

use Innoboxrr\LaravelOptions\Models\Option;
use Innoboxrr\SearchSurge\Search\Builder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OptionsExports implements FromView
{

    protected $data;

    public function __construct( array $data) 
    {

        $this->data = $data;

    }

    public function view(): View
    {
        return view(
            config(
                'innoboxrrlaraveloptions.excel_view', 
                'innoboxrrlaraveloptions::excel.'
            ) . 'option', 
            [
                'options' => $this->getQuery(),
                'exportCols' => Option::$export_cols
            ]
        );
    }

    public function getQuery()
    {   

        $builder = new Builder();

        return $builder->get(Option::class, $this->data);

    }

}