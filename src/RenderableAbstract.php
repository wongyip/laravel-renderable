<?php namespace Wongyip\Laravel\Renderable;

use Illuminate\Database\Eloquent\Model;
use Wongyip\Laravel\Renderable\Components\ColumnOptions;
use Wongyip\Laravel\Renderable\Traits\RenderableGetSetters;
use Wongyip\Laravel\Renderable\Traits\RenderableLabels;
use Wongyip\Laravel\Renderable\Traits\RenderableMacros;
use Wongyip\Laravel\Renderable\Traits\RenderableModel;
use Wongyip\Laravel\Renderable\Traits\RenderableTypes;

abstract class RenderableAbstract implements RenderableInterface
{
    use RenderableGetSetters, RenderableLabels, RenderableModel, RenderableTypes;

    /**
     * Source Attributes for Rendering
     *
     * @var array
     */
    protected array $attributes = [];
    /**
     * Columns to be rendered, unless specified in $this->excluded.
     *
     * @var array|string[]
     */
    protected array $columns = [];
    /**
     * Columns to be rendered in raw HTML.
     *
     * @var array|string[]
     */
    protected array $columnsHTML = [];
    /**
     * Renderable options of columns.
     *
     * @var array|ColumnOptions[]
     */
    protected array $columnsOptions = [];
    /**
     * Columns NOT to be rendered.
     *
     * @var array|string[]
     */
    protected array $excluded = [];
    /**
     * Column labels in plain-text.
     *
     * @var array|string[]
     */
    protected array $labels = [];
    /**
     * Columns labels that should be rendered as HTML (Twig: |raw filter).
     *
     * @var array|string[]
     */
    protected array $labelsHTML = [];
    /**
     * Layout for view lookup.
     *
     * @var string
     */
    protected string $layout = '';
    /**
     * The input model object.
     *
     * @see RenderableMacros::model()
     * @var Model
     */
    protected Model $model;
    /**
     * Type of columns.
     *
     * @var array|string[]
     */
    protected array $types = [];

    /**
     * @inheritdoc
     */
    public function attribute(string $column): mixed
    {
        if (key_exists($column, $this->attributes)) {
            return $this->attributes[$column];
        }
        return isset($this->model)
            ? $this->model->$column
            : null;
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
        $fromLayout = $this->layout;
        $this->layout = $layout;
        // Changed?
        if ($fromLayout !== $layout) {
            $layoutMethod = 'layout' . ucfirst($layout);
            if (method_exists($this, $layoutMethod)) {
                return $this->$layoutMethod();
            }
        }
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