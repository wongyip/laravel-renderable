<?php

namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Database\Eloquent\Model;
use Wongyip\Laravel\Renderable\Components\RenderableOptions;
use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\Laravel\Renderable\Utils\Bootstrap;

/**
 * @todo Work in progress.
 */
trait LayoutGrid
{
    /**
     * Instantiate a Renderable object in grid layout.
     *
     * @param array|Model $attributes Source attributes array or Eloquent Model.
     * @param array|string[]|string|bool|null $included Default true for all columns.
     * @param array|string[]|string|null $excluded Default null for none.
     * @param array|RenderableOptions|null $options Custom options, skip to taking values from config('renderable.options').
     * @return static
     */
    static function grid(array|Model $attributes, array|string|bool $included = null, array|string $excluded = null, array|RenderableOptions $options = null): static
    {
        return new static($attributes, $included, $excluded, $options, Renderable::LAYOUT_GRID);
    }

    /**
     * @return static
     */
    protected function layoutGrid(): static
    {
        $this->table->tagName('div')->class(['renderable-grid', 'row']);
        $this->fieldHeader->tagName('div');
        $this->valueHeader->tagName('div');
        $this->container->classRemove(Bootstrap::classTableResponsive());
        return $this;
    }
}