<?php namespace Wongyip\Laravel\Renderable;

use Wongyip\Laravel\Renderable\Traits\RenderableGetSetters;
use Wongyip\Laravel\Renderable\Traits\RenderableLabels;
use Wongyip\Laravel\Renderable\Traits\RenderableTypes;

abstract class RenderableAbstract implements RenderableInterface
{
    use RenderableLabels, RenderableGetSetters, RenderableTypes;
    
    /**
     * Columns to be rendered, unless specified in $this->excluded.
     *
     * @var array|string[]
     */
    protected array $columns = [];
    /**
     * Columns NOT to be rendered.
     *
     * @var array|string[]
     */
    protected array $excluded = [];
    /**
     * Columns to be rendered in raw HTML.
     *
     * @var array|string[]
     */
    protected array $columnsHTML = [];
    /**
     * Layout for view lookup.
     *
     * @var string
     */
    protected string $layout = '';
    /**
     * Source Attributes for Rendering
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * @inheritdoc
     */
    public function attribute(string $column): mixed
    {
        if (key_exists($column, $this->attributes)) {
            return $this->attributes[$column];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function attributes(array $attributes = null): array|static
    {
        if (is_array($attributes)) {
            $this->attributes = $attributes;
            return $this;
        }
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function columns(array|string|bool $columns = null, bool $replace = false): array|static
    {
        // Get
        if (is_null($columns)) {
            return array_diff($this->columns, $this->excluded);
        }
        // Set
        $columns = $columns === true ? array_keys($this->attributes()) : $columns;
        $this->columns = $replace ? $columns : array_merge($this->columns, $columns);
        return $this;
    }
    
    /**
     * @inheritdoc 
     */
    public function columnsHTML(string|array|bool $columns = null, bool $replace = false): array|static
    {
        // Get
        if (is_null($columns)) {
            return array_diff($this->columnsHTML, $this->excluded);
        }
        // Set
        $this->columnsHTML = $replace ? $columns : array_merge($this->columnsHTML, $columns);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function exclude(string|array $columns = null, bool $replace = false): array|static
    {
        // Get
        if (is_null($columns)) {
            return $this->excluded;
        }
        // Set
        $this->excluded = $replace ? $columns : array_merge($this->excluded, $columns);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function layout(string $layout = null): string|static
    {
        // Get
        if (is_null($layout)) {
            return $this->layout;
        }
        // Set
        $this->layout = $layout;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public abstract function renderables(): array;

    /**
     * @inheritdoc
     */
    public function view(): string
    {
        return LARAVEL_RENDERABLE_VIEW_NAMESPACE . '::' . $this->layout();
    }
}