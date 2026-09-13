<?php

namespace Innoboxrr\LaravelOptions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Innoboxrr\Traits\MetaOperations;
use Innoboxrr\LaravelOptions\Models\Traits\Relations\OptionRelations;
use Innoboxrr\LaravelOptions\Models\Traits\Storage\OptionStorage;
use Innoboxrr\LaravelOptions\Models\Traits\Assignments\OptionAssignment;
use Innoboxrr\LaravelOptions\Models\Traits\Operations\OptionOperations;
use Innoboxrr\LaravelOptions\Models\Traits\Mutators\OptionMutators;

class Option extends Model
{

    use HasFactory,
        SoftDeletes,
        MetaOperations,
        OptionRelations,
        OptionStorage,
        OptionAssignment,
        OptionOperations,
        OptionMutators;

    protected $fillable = [
        'name',
        'key',
        'value',
    ];

    protected $creatable = [
        'name',
        'key',
        'value',
    ];

    protected $updatable = [
        'name',
        'key',
        'value',
    ];

    protected $casts = [];

    protected $protected_metas = [];

    protected $editable_metas = [];

    public static $export_cols = [
        'name',
        'key',
        'value',
    ];

    public static $loadable_relations = [];

    public static $loadable_counts = [];

    protected static function newFactory()
    {
        return \Innoboxrr\LaravelOptions\Database\Factories\OptionFactory::new();
    }

    /**
     * El valor de una opcion, por su clave.
     *
     * Las opciones estructuradas (theme, por ejemplo) se guardan como JSON:
     * si el valor es un objeto o un arreglo JSON se devuelve como arreglo, y
     * cualquier otro valor tal cual, como texto. Si la clave no existe, esta
     * borrada o su valor es null, devuelve $default.
     *
     * No hay cache: cada llamada es una consulta.
     */
    public static function value(string $key, mixed $default = null): mixed
    {
        $value = static::query()->where('key', $key)->value('value');

        if ($value === null) {
            return $default;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : $value;
    }

    /**
     * value es a la vez columna y el metodo de arriba. Eloquent toma por
     * relacion todo metodo que se llame como un atributo no cargado: sin esto,
     * leer $option->value de una fila consultada sin esa columna llamaria a
     * value() sin argumentos.
     */
    public function isRelation($key)
    {
        return $key !== 'value' && parent::isRelation($key);
    }

}
