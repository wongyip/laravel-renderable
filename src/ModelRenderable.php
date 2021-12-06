<?php namespace Wongyip\Laravel\Renderable;

use Illuminate\Database\Eloquent\Model;
use Wongyip\Laravel\Renderable\Traits\LabelsTrait;
use Wongyip\Laravel\Renderable\Traits\RenderableTrait;
use Wongyip\Laravel\Renderable\Traits\StylingPropertiesTrait;
use Wongyip\Laravel\Renderable\Traits\TypeTrait;

class ModelRenderable implements RenderableInterface
{
    use LabelsTrait, RenderableTrait, StylingPropertiesTrait, TypeTrait;
    
    /**
     * @var string
     */
    const DEFAULT_COLUMN_TYPE = 'string';
    /**
     * @var string
     */
    const DEFAULT_LAYOUT      = 'table';
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
     * @var Model
     */
    protected $model;
    
    /**
     *
     * Note to $columns: default true to render all columns retrieved by $model->toArray() method.
     *
     * @param Model           $model
     * @param string[]|string $columns
     * @param string[]|string $excluded
     * @param boolean         $autoLabels
     * @param string          $layout
     */
    public function __construct(Model $model, $columns = true, $excluded = null, $autoLabels = true, $layout = null)
    {
        // Defaults
        $this->containerId = uniqid('mr-');
        
        // Take params
        $this->model = $model;
        $this->columns($columns);
        $this->exclude($excluded);
        $this->layout($layout = is_string($layout) ? $layout : self::DEFAULT_LAYOUT);
        
        // Automation
        if ($autoLabels) {
            $this->autoLabels();
        }
        
        // Integration
        if (method_exists($model, 'getLabels')) {
            try {
                $this->labels($model->getLabels());
            }
            catch (\Exception $e) {
                // Let it be...
            }
        }
    }
    
    /**
     * @param Model           $model
     * @param string[]|string $columns
     * @param string[]|string $excluded
     * @param boolean         $autoLabels
     * @return \Wongyip\Laravel\Renderable\ModelRenderable
     */
    static function table(Model $model, $columns = true, $excluded = null, $autoLabels = true)
    {
        return new static($model, $columns, $excluded, $autoLabels, self::LAYOUT_TABLE);
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
        return $this->getSetColumnsProp('columnsHTML', $columns, $replace);
    }
    
    /**
     * @return \Wongyip\Laravel\Renderable\ColumnRenderable[]
     */
    public function renderables()
    {
        $renderables = [];
        if ($columns = $this->columns()) {
            foreach ($columns as $column) {
                $renderable = new ColumnRenderable();
                $renderable->name      = $column;
                $renderable->isHTML    = $this->isHTML($column);
                $renderable->label     = $this->label($column);
                $renderable->labelHTML = $this->labelHTML($column);
                $renderable->type      = $this->type($column);
                $renderable->value     = $this->value($column);
                $renderables[] = $renderable;
            }
        }
        return $renderables;
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
     * Determine if a column should be rendered as raw HTML.
     * @param string $column
     * @return boolean|NULL
     */
    public function isHTML($column)
    {
        if (is_array($this->columnsHTML)) {
            return in_array($column, $this->columnsHTML);
        }
        return null;
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
     * Get the model. (NO SETTER)
     *
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Model|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function model()
    {
        // Dev note: changing of model after instanation is not a good idea.
        return $this->model;
    }
    
    /**
     * @param string $column
     * @return boolean
     */
    public function shouldRender($column)
    {
        if (!in_array($column, $this->excluded)) {
            return in_array($column, $this->columns);
        }
        return false;
    }
    
    /**
     * Get value of a column. (NO SETTER)
     *
     * @param string $column
     * @return mixed|NULL
     */
    public function value($column)
    {
        // No model no take larr.
        if ($this->model) {
            
            // Value from model.
            $value = $this->model->getAttribute($column);
            
            // Type defined locally.
            $type = $this->type($column);
            
            // Currently only boolean type will be stored as array.
            if (is_array($type)) {
                
                // In case of null and there is a null-replacement.
                if (is_null($value) && key_exists('valueNull', $type)) {
                    return $type['valueNull'];
                }
                
                // NULL as false now.
                return boolval($value) ? $type['valueTrue'] : $type['valueFalse'];
            }
            
            // These type should be array.
            switch ($type) {
                case 'boolean':
                    // @todo assumed exists...
                    $options = $this->typesOptions[$column];
                    // In case of null and there is a null-replacement.
                    if (is_null($value) && key_exists('valueNull', $options)) {
                        return $options['valueNull'];
                    }
                    // NULL as false now.
                    return boolval($value) ? $options['valueTrue'] : $options['valueFalse'];
                    // No break...
                case 'csv':
                    return is_array($value) ? implode(', ', $value) : $value;
                    // No break...
                case 'ol':
                case 'ul':
                    // Must be array, so the view could handle it corretly.
                    return is_array($value) ? $value : [$value];
            }
            
            // DateTime to string
            if ($value instanceof \DateTime) {
                return $value->format(MODEL_RENDERABLE_DATETIME_FORMAT);
            }
            
            // GIGO
            return $value;
        }
        return null;
    }
}