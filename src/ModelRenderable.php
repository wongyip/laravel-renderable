<?php namespace Wongyip\Laravel\Renderable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Wongyip\PHPHelpers\Format;

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

        // dd($attributes);

        // Init
        parent::__construct($attributes, $columns, $excluded, $autoLabels, $layout);
        
        // Integration
        if (method_exists($model, 'getLabels')) {
            try {
                $this->labels($model->getLabels());
            }
            catch (Exception $e) {
                // Let it be...
            }
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
        else {
            // dd($this->model->$column, $this->labels, $this->model->getAttributes());
            return $this->model->$column;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function label($column, $label = null)
    {
        $returns = $this->getSetPropAssoc('labels', $column, $label);
        if (!is_null($returns)) {
            return $returns;
        }
        // Not set.
        try {
            if ($this->model && method_exists($this->model, 'getLabel')) {
                $output = $this->model->getLabel($column);
                if (is_string($output) && !empty($output)) {
                    return $output;
                }
            }
        }
        catch (Exception $e) {
            Log::error('ModelRenderable.label() exception: ' . $e->getMessage());
        }
        return Format::smartCaps($column);
    }

    /**
     * Get the model. (NO SETTER)
     *
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Model
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
     * @return static
     */
    static function table($model, $columns = true, $excluded = null, $autoLabels = true)
    {
        return new static($model, $columns, $excluded, $autoLabels, self::LAYOUT_TABLE);
    }
}