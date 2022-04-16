<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\ModelRenderable;
use Wongyip\Laravel\Renderable\Renderable;

/**
 * @author wongyip
 */
trait TypeTrait
{
    /**
     * Types of columns.
     *
     * @var string[]
     */
    protected $types = [];
    
    /**
     * Get or set data type of a column, where setter support an array of columns as input.
     * 
     * Note that certain setter methods, e.g. typeBoolean(), is recommended to
     * use when settting data type if there are options bound to that data type.
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
        $cols = is_array($column) ? $column : [$column];
        
        // Validate options
        if (!is_null($options) && !is_array($options)) {
            throw new \Exception('Input $options must be an array.');
        }
        
        foreach ($cols as $col) {
            // Set type
            $this->types[$col] = $type;
            // Set options
            if (is_array($options)) {
                $this->options($col, $options);
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
        $valueTrue  = is_null($valueTrue)  ? Renderable::DEFAULT_VALUE_BOOL_TRUE : $valueTrue;
        $valueFalse = is_null($valueFalse) ? Renderable::DEFAULT_VALUE_BOOL_FALSE : $valueFalse;
        $valueNull  = is_null($valueNull)  ? $valueFalse : $valueNull;
        $options = compact('valueTrue', 'valueFalse', 'valueNull');
        
        
        
        // dd(__FILE__, __LINE__, $options);
        
        
        
        return $this->type($column, 'boolean', $options);
    }
    
    /**
     * Type column(s) as CSV.
     *
     * @param string|string[] $column
     * @param string          $glue
     * @return \Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function typeCSV($column, $glue = null)
    {
        $glue = is_string($glue) ? $glue : Renderable::DEFAULT_CSV_GLUE;
        $options = compact('glue');
        return $this->type($column, 'csv', $options);
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