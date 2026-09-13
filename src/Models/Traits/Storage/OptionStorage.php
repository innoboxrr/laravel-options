<?php

namespace Innoboxrr\LaravelOptions\Models\Traits\Storage;

// use Innoboxrr\LaravelOptions\Models\OptionMeta;

trait OptionStorage
{

    public function createModel($request)
    {

        $option = $this->create($request->only($this->creatable));

        return $option;

    }

    public function updateModel($request)
    {

        $this->update($request->only($this->updatable));

        return $this;

    }

    /*
    public function updateModelMetas($request)
    {

        $this->update_metas($request, OptionMeta::class, 'option_id')->updatePayload();

        return $this;

    }
    */

    public function deleteModel()
    {

        $this->delete();

    }

    public function restoreModel()
    {

        $this->restore();

    }

    /**
     * Quien puede borrar para siempre lo decide la politica (forceDelete nace
     * apagado ahi), no un abort escondido aqui que la API de politicas no ve.
     */
    public function forceDeleteModel()
    {

        $this->forceDelete();

    }

}
