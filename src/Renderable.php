<?php namespace Wongyip\Laravel\Renderable;

use Wongyip\Laravel\Renderable\Traits\Bootstrap4Trait;
use Wongyip\Laravel\Renderable\Traits\LabelsTrait;
use Wongyip\Laravel\Renderable\Traits\OptionsTrait;
use Wongyip\Laravel\Renderable\Traits\PublicPropTrait;
use Wongyip\Laravel\Renderable\Traits\RenderableTrait;
use Wongyip\Laravel\Renderable\Traits\RenderingOptionsTrait;
use Wongyip\Laravel\Renderable\Traits\TypeTrait;

/**
 * The base implementation of RenderableInterface.
 * 
 * @author wongyip
 */
class Renderable implements RenderableInterface
{
    use Bootstrap4Trait, LabelsTrait, OptionsTrait, PublicPropTrait, RenderableTrait, RenderingOptionsTrait, TypeTrait;
    
    /**
     * @var string
     */
    const DEFAULT_COLUMN_TYPE = 'string';
    /**
     * @var string
     */
    const DEFAULT_CSV_GLUE = ', ';
    /**
     * @var string
     */
    const DEFAULT_LAYOUT = 'table';
    /**
     * @var string
     */
    const DEFAULT_VALUE_BOOL_TRUE = 'Yes';
    /**
     * @var string
     */
    const DEFAULT_VALUE_BOOL_FALSE = 'No';
    /**
     * Simple single table layout, with two columns (Field & Value).
     *
     * @var string
     */
    const LAYOUT_TABLE = 'table';
    /**
     * Columnes-rows based grid system, like Bootstrap.
     *
     * @var string
     */
    const LAYOUT_GRID  = 'grid';
    
    /**
     * @var array
     */
    protected $booleanDefault = ['Yes', 'No'];
    /**
     * Columns that should be rendered, ***unless specified excluded.
     * @var string[]
     */
    protected $columns = [];
    /**
     * @var string[]
     */
    protected $excluded = [];
    /**
     * Columns that should be rendered in raw HTML.
     * @var string[]
     */
    protected $columnsHTML = [];
    /**
     * @var string
     */
    protected $layout;
    /**
     * @var array
     */
    protected $attributes;
    /**
     * Types of columns.
     *
     * @var string[]
     */
    protected $types = [];
    
    /**
     * Instantiate a Renderable object (in 'table' layout by default).
     * 
     * @param array           $attributes
     * @param string[]|string $columns
     * @param string[]|string $excluded
     * @param boolean         $autoLabels
     * @param string          $layout
     */
    public function __construct($attributes, $columns = true, $excluded = null, $autoLabels = true, $layout = null)
    {
        // Defaults
        $this->containerId = uniqid('mr-');
        
        // Take params
        $this->attributes = $attributes;
        $this->columns($columns);
        $this->exclude($excluded);
        $this->layout($layout = is_string($layout) ? $layout : self::DEFAULT_LAYOUT);
        
        // Automation
        if ($autoLabels) {
            $this->autoLabels();
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::attribute()
     */
    public function attribute($column)
    {
        if (is_array($this->attributes) && key_exists($column, $this->attributes)) {
            return $this->attributes[$column];
        }
        return null;
    }
    
    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::attributes()
     */
    public function attributes()
    {
        return is_array($this->attributes) ? $this->attributes : [];
    }
    
    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::columns()
     */
    public function columns($columns = null, $replace = false)
    {
        // Automation
        if ($columns === true) {
            $columns = array_keys($this->attributes());
        }
        // Actual setter.
        $this->getSetColumnsProp('columns', $columns, $replace);
        
        // Get
        if (is_null($columns)) {
            return array_diff($this->columns, $this->excluded);
        }
        // Set
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::columnsHTML()
     */
    public function columnsHTML($columns = null, $replace = false)
    {
        $this->options($columns, ['html' => true]);
        return $this->getSetColumnsProp('columnsHTML', $columns, $replace);
    }
    
    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::exclude()
     */
    public function exclude($excluded = null, $replace = false)
    {
        return $this->getSetColumnsProp('excluded', $excluded, $replace);
    }
    
    /**
     * Alias to the columns() method.
     */
    public function include($columns = null, $replace = false)
    {
        return $this->columns($columns, $replace);
    }
    
    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::layout()
     */
    public function layout($layout = null)
    {
        return $this->getSetProp('layout', $layout);
    }
    
    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::renderables()
     */
    public function renderables()
    {
        $renderables = [];
        if ($columns = $this->columns()) {
            foreach ($columns as $column) {
                $renderable = new ColumnRenderable();
                $renderable->name      = $column;
                $renderable->label     = $this->label($column);
                $renderable->labelHTML = $this->labelHTML($column);
                $renderable->options   = $this->options($column);
                $renderable->type      = $this->type($column);
                $renderable->value     = $this->value($column);
                $renderables[] = $renderable;
            }
        }
        return $renderables;
    }
    
    /**
     * Instantiate a Renderable object in 'table' layout.
     *
     * @param array           $attributes
     * @param string[]|string $columns     Default true for all columns.
     * @param string[]|string $excluded    Default null for none.
     * @param boolean         $autoLabels  Default true.
     * @return static
     */
    static function table($attributes, $columns = true, $excluded = null, $autoLabels = true)
    {
        return new static($attributes, $columns, $excluded, $autoLabels, self::LAYOUT_TABLE);
    }
    
    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::view()
     */
    public function view()
    {
        return LARAVEL_RENDERABLE_VIEW_NAMESPACE . '::' . $this->layout();
    }
    
    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::value()
     */
    public function value($column)
    {
        // The original value.
        $value = $this->attribute($column);
        
        // Type defined locally.
        $type = $this->type($column);
        $options = $this->options($column);
        
        // These type should be array.
        switch ($type) {
            case 'boolean':
                // In case of null and there is a null-replacement.
                if (is_null($value) && key_exists('valueNull', $options)) {
                    return $options['valueNull'];
                }
                // NULL as false now.
                return boolval($value) ? $options['valueTrue'] : $options['valueFalse'];
            case 'csv':
                // @todo what if $value is not scalar?
                return is_array($value) ? implode($options['glue'], $value) : $value;
            case 'ol':
            case 'ul':
                // Must be array, so the view could handle it corretly.
                return is_array($value) ? $value : [$value];
            default:
                // DateTime to string
                if (is_object($value)) {
                    if ($value instanceof \DateTime) {
                        return $value->format(LARAVEL_RENDERABLE_DATETIME_FORMAT);
                    }
                }
                if (is_array($value)) {
                    // Output array values as CSV by default.
                    return implode(Renderable::DEFAULT_CSV_GLUE, array_values($value));
                }
                if (is_bool($value)) {
                    return $value ? Renderable::DEFAULT_VALUE_BOOL_TRUE : Renderable::DEFAULT_VALUE_BOOL_FALSE;
                }
        }
        // GIGO
        return $value;
    }
}