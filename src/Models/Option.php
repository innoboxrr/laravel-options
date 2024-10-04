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

}
