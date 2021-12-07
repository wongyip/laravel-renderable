<?php namespace Wongyip\Laravel\Renderable;

use Wongyip\Laravel\Renderable\Traits\LabelsTrait;
use Wongyip\Laravel\Renderable\Traits\OptionsTrait;
use Wongyip\Laravel\Renderable\Traits\PublicPropTrait;
use Wongyip\Laravel\Renderable\Traits\RenderableTrait;
use Wongyip\Laravel\Renderable\Traits\TypeTrait;

/**
 * The base implementation of RenderableInterface.
 * 
 * @author wongyip
 */
class Renderable implements RenderableInterface
{
    use LabelsTrait, OptionsTrait, PublicPropTrait, RenderableTrait, TypeTrait;
    
    /**
     * @var string
     */
    protected $_viewPrefix = 'renderable::model-';
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
     * @param array $attributes
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }
    
    /**
     * [The actual method that] Get value of a column. (NO SETTER)
     *
     * @param string $column
     * @return mixed|NULL
     */
    protected function _value($column)
    {
        if (is_array($this->attributes)) {
            if (key_exists($column, $this->attributes)) {
                return $this->attributes[$column];
            }
        }
        return null;
    }
    
    /**
     * Get all attributes as an associative array. 
     * 
     * @return array
     */
    public function attributes()
    {
        return is_array($this->attributes) ? $this->attributes : [];
    }
    
    /**
     * Get or set columns that should be rendered (unless specified excluded).
     *
     * Setter:
     *   1. will take all columns of $model->toArray() if $columns is set to TRUE,
     *   2. will merge the columns into existing $columns unless $replace is TRUE.
     *
     * @param string[]|string $columns
     * @param boolean         $replace
     * @return string[]|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function columns($columns = null, $replace = false)
    {
        // Automation
        if ($columns === true) {
            $columns = array_keys($this->model->toArray());
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
     * Get or set columns that should be rendered with the |raw filter..
     *
     * Setter:
     *   1. will take all columns of $model->toArray() if $columns is set to TRUE,
     *   2. will merge the columns into existing $columns unless $replace is TRUE.
     *
     * @param string[]|string $columns
     * @param boolean         $replace
     * @return string[]|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function columnsHTML($columns = null, $replace = false)
    {
        $this->options($columns, ['html' => true]);
        return $this->getSetColumnsProp('columnsHTML', $columns, $replace);
    }
    
    /**
     * Get or set columns that should be excluded from rendering.
     *
     * Setter:
     *   1. will merge the columns into existing $columns unless $replace is TRUE.
     *   2. put an empty array as $excluded and set $replace to TRUE empty the list.
     *
     * @param string[]|string $excluded
     * @param boolean         $replace
     * @return string[]|\Wongyip\Laravel\Renderable\ModelRenderable
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
     * Get or set the layout.
     *
     * @param string $layout
     * @return string|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function layout($layout = null)
    {
        return $this->getSetProp('layout', $layout);
    }
    
    /**
     * Get an array of compiled ColumnRederable objects for rendering.
     *
     * @return \Wongyip\Laravel\Renderable\ColumnRenderable[]
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
     * Get value of a column. (NO SETTER)
     *
     * @param string $column
     * @return mixed|NULL
     */
    public function value($column)
    {
        // Value from model.
        $value = $this->_value($column);
        
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
                return is_array($value) ? implode($options['glue'], $value) : $value;
                // Must be array, so the view could handle it corretly.
            case 'ol':
            case 'ul':
                return is_array($value) ? $value : [$value];
            default:
                // DateTime to string
                if ($value instanceof \DateTime) {
                    return $value->format(MODEL_RENDERABLE_DATETIME_FORMAT);
                }
        }
        // GIGO
        return $value;
    }
}