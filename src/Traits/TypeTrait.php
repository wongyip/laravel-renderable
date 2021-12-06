<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\ModelRenderable;

trait TypeTrait
{
    /**
     * Types of columns.
     *
     * @var string[]
     */
    protected $types = [];
    /**
     * Type-specific options of columns.
     *
     * @var mixed[]
     */
    protected $typesOptions = [];
    
    /**
     * Get|set of column data type.
     *
     * @param string|string[] $column
     * @param string          $type
     * @param mixed           $options
     * @return string|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function type($column, $type = null, $options = null)
    {
        // Get
        if (is_null($type)) {
            // @todo assumed $column is string here
            return key_exists($column, $this->types) ? $this->types[$column] : ModelRenderable::DEFAULT_COLUMN_TYPE;
        }
        // Set
        $cols = is_array($column) ? $column : [$column];
        foreach ($cols as $col) {
            $this->types[$col] = $type;
            if (!is_null($options)) {
                $this->typesOptions[$col] = $options;
            }
        }
        return $this;
    }
    
    /**
     * Type column(s) as Boolean.
     *
     * @param string|string[] $column
     * @param string          $valueTrue  Default 'Yes'
     * @param string          $valueFalse Default 'No'
     * @param string          $valueNull  Default to $valueFalse
     * @return \Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function typeBoolean($column, $valueTrue = null, $valueFalse = null, $valueNull = null)
    {
        $valueTrue  = is_null($valueTrue)  ? 'Yes' : $valueTrue;
        $valueFalse = is_null($valueFalse) ? 'No' : $valueFalse;
        $valueNull  = is_null($valueNull)  ? $valueFalse : $valueNull;
        return $this->type($column, 'boolean', compact('valueTrue', 'valueFalse', 'valueNull'));
    }
    
    /**
     * Type column(s) as CSV.
     *
     * @param string|string[] $column
     * @return \Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function typeCSV($column)
    {
        return $this->type($column, 'csv');
    }
    
    /**
     * Type column(s) as Ordered List.
     *
     * @param string|string[] $column
     * @return \Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function typeOL($column)
    {
        return $this->type($column, 'ol');
    }
    
    /**
     * Type column(s) as multi-line text, which will be rendered with |nl2br filter.
     * 
     * Note: no effect if column is declared as HTML.
     *
     * @param string|string[] $column
     * @return \Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function typeText($column)
    {
        return $this->type($column, 'text');
    }
    
    /**
     * Type column(s) as Unordered List.
     *
     * @param string|string[] $column
     * @return \Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function typeUL($column)
    {
        return $this->type($column, 'ul');
    }
}