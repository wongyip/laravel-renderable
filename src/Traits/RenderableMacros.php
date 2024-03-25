<?php namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Database\Eloquent\Model;
use Wongyip\Laravel\Renderable\Renderable;

trait RenderableMacros
{
    /**
     * Get or set the field header style.
     *
     * @param string|null $setter
     * @param bool|null $keepExisting
     * @return string|null|Renderable
     */
    public function fieldHeaderStyle(string $setter = null, bool $keepExisting = null): string|null|Renderable
    {
        if ($setter) {
            if (!$keepExisting) {
                $this->fieldHeader->styleEmpty();
            }
            $this->fieldHeader->styleAdd($setter);
            return $this;
        }
        return $this->fieldHeader->style();
    }

    /**
     * Get or set the field header's width in pixels.
     *
     * @param int|null $setter
     * @return int|null|Renderable
     */
    public function fieldHeaderWidth(int $setter = null): int|null|Renderable
    {
        if ($setter) {
            $this->fieldHeader->styleAdd('width', $setter . 'px');
            return $this;
        }
        return preg_match("/^(\d+)px/",  $this->fieldHeader->style('width'), $matches)
            ? (int) $matches[1]
            : null;
    }

    /**
     * Shorthand method to instantiate a Renderable object for fast chaining.
     *
     * @param array|Model $attributes Input attributes, also accepts Eloquent Model.
     * @param array|true|string $columns Default true for all columns.
     * @param array|string|null $excluded Default null for none.
     * @param string|null $layout Default to config('renderable.default.layout').
     * @return static
     */
    public static function make(array|Model $attributes, array|true|string $columns = true, array|string $excluded = null, string $layout = null): static
    {
        return new static($attributes, $columns, $excluded, $layout);
    }

    /**
     * Instantiate a Renderable object with an Eloquent Model.
     *
     * @param Model $model
     * @param array|true|string $columns Default true for all columns.
     * @param array|string|null $excluded Default null for none.
     * @param string|null $layout Default to config('renderable.default.layout').
     * @return static
     */
    static function model(Model $model, array|true|string $columns = true, array|string $excluded = null, string $layout = null): static
    {
        $attributes = $model->toArray();
        return new static($attributes, $columns, $excluded, $layout ?? config('renderable.default.layout'));
    }

    /**
     * Instantiate a Renderable object in 'table' layout.
     *
     * @param array|Model $attributes Input attributes, also accepts Eloquent Model.
     * @param array|true|string $columns Default true for all columns.
     * @param array|string|null $excluded Default null for none.
     * @return static
     */
    static function grid(array|Model $attributes, array|true|string $columns = true, array|string $excluded = null): static
    {
        return new static($attributes, $columns, $excluded, Renderable::LAYOUT_TABLE);
    }

    /**
     * Instantiate a Renderable object in 'table' layout.
     * @param array|Model $attributes Input attributes, also accepts Eloquent Model.
     * @param array|true|string $columns Default true for all columns.
     * @param array|string|null $excluded Default null for none.
     * @return static
     */
    static function table(array|Model $attributes, array|true|string $columns = true, array|string $excluded = null): static
    {
        return new static($attributes, $columns, $excluded, Renderable::LAYOUT_TABLE);
    }

}