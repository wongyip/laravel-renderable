<?php namespace Wongyip\Laravel\Renderable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @deprecated Keep to keep old code "less broken".
 */
class ModelRenderable extends Renderable
{
    /**
     * Note to $columns: default true to render all columns retrieved by $model->toArray() method.
     *
     * @param Model $model
     * @param string[]|string $columns Default true for all columns.
     * @param string[]|string $excluded Default null for none.
     * @param bool $autoLabels Default true.
     * @param string $layout Default null to respect Renderable::DEFAULT_LAYOUT.
     */
    public function __construct(Model $model, $columns = true, $excluded = null, $autoLabels = true, $layout = null)
    {
        Log::warning('Deprecated ModelRenderable, use Renderable::model() instead.');
        parent::__construct($model, $columns, $excluded, $layout);
    }

    /**
     * Instantiate a Renderable object in 'table' layout.
     *
     * @param array|Model $attributes
     * @param array|true|string $columns Default true for all columns.
     * @param array|string|null $excluded Default null for none.
     * @return static
     */
    static function table(array|Model $attributes, array|true|string $columns = true, array|string $excluded = null): static
    {
        Log::warning('Deprecated ModelRenderable::table(), use Renderable::model()->renderAsTable() instead.');
        return self::make($attributes, $columns, $excluded)->renderAsTable();

    }
}