<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Renderable;

trait RenderableMacros
{
    /**
     * Instantiate a Renderable object in 'table' layout.
     *
     * @param array $attributes
     * @param true|string|string[] $columns Default true for all columns.
     * @param string|string[]|null $excluded Default null for none.
     * @param boolean $autoLabels  Default true.
     * @return static
     */
    static function table(array $attributes, array|true|string $columns = true, array|string $excluded = null, bool $autoLabels = true): static
    {
        return new static($attributes, $columns, $excluded, $autoLabels, Renderable::LAYOUT_TABLE);
    }
}