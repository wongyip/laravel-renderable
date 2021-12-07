<?php namespace Wongyip\Laravel\Renderable;

use Illuminate\Database\Eloquent\Model;

class ModelRenderable extends Renderable
{
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
     * @var Model
     */
    protected $model;
    
    /**
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
     * [The actual method that] Get value of a column. (NO SETTER)
     *
     * @param string $column
     * @return mixed|NULL
     */
    protected function _value($column)
    {
        // No model no take larr.
        if ($this->model) {
            // Value from model.
            return $this->model->getAttribute($column);
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
        if (is_object($this->model) && $this->model instanceof Model) {
            return $this->model->getAttributes();
        }
        return [];
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
     * Instantiate a ModelRenderable object in 'table' layout.
     * 
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
}