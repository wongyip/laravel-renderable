<?php namespace Wongyip\Laravel\Renderable;

use Illuminate\Database\Eloquent\Model;

class ModelRenderable extends Renderable
{   
    /**
     * @var Model
     */
    protected $model;
    
    /**
     * Note to $columns: default true to render all columns retrieved by $model->toArray() method.
     *
     * @param Model           $model
     * @param string[]|string $columns     Default true for all columns.
     * @param string[]|string $excluded    Default null for none.
     * @param boolean         $autoLabels  Default true.
     * @param string          $layout      Default null to repect Renderable::DEFAULT_LAYOUT.
     */
    public function __construct(Model $model, $columns = true, $excluded = null, $autoLabels = true, $layout = null)
    {   
        // Take params
        $this->model = $model; 
        
        // Note here, use toArray() instead of getAttributes() since the former
        // returns mutated/casted attributes, while the latter returns original
        // values from schema.
        $attributes = $model->toArray();
     
        // Init
        parent::__construct($attributes, $columns, $excluded, $autoLabels, $layout);
        
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
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::attributes()
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
     * @param string[]|string $columns     Default true for all columns.
     * @param string[]|string $excluded    Default null for none.
     * @param boolean         $autoLabels  Default true.
     * @return \Wongyip\Laravel\Renderable\ModelRenderable
     */
    static function table(Model $model, $columns = true, $excluded = null, $autoLabels = true)
    {
        return new static($model, $columns, $excluded, $autoLabels, self::LAYOUT_TABLE);
    }
}