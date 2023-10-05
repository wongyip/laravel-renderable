<?php namespace Wongyip\Laravel\Renderable\Traits;

use Exception;
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
     * Get or set data type of column, where setter support an array of columns
     * as input.
     *
     * Implementation note: Certain setter methods, e.g. typeBoolean(), is
     * recommended when setting data type if there are options bound to that
     * data type.
     *
     * @param string|string[] $column
     * @param string          $type
     * @param array|null      $options
     * @return string|static
     */
    public function type($column, $type = null, array $options = null)
    {
        // Get
        if (is_null($type)) {
            /*
             * Strict mode
             *
            if (!is_string($column)) {
                throw new Exception('Input "column" must be string when getting data-type of a column.');
            }
            */
            return (is_string($column) && key_exists($column, $this->types)) ? $this->types[$column] : Renderable::DEFAULT_COLUMN_TYPE;
        }

        // Set
        $cols = is_array($column) ? $column : [$column];
        foreach ($cols as $col) {
            $this->types[$col] = $type;
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
     * @return static
     */
    public function typeBoolean($column, $valueTrue = null, $valueFalse = null, $valueNull = null)
    {
        $valueTrue  = is_null($valueTrue)  ? Renderable::DEFAULT_VALUE_BOOL_TRUE : $valueTrue;
        $valueFalse = is_null($valueFalse) ? Renderable::DEFAULT_VALUE_BOOL_FALSE : $valueFalse;
        $valueNull  = is_null($valueNull)  ? $valueFalse : $valueNull;
        $options = compact('valueTrue', 'valueFalse', 'valueNull');
        return $this->type($column, 'boolean', $options);
    }

    /**
     * Type column(s) as CSV.
     *
     * @param string|string[] $column
     * @param string          $glue
     * @return static
     */
    public function typeCSV($column, $glue = null)
    {
        $glue = is_string($glue) ? $glue : Renderable::DEFAULT_CSV_GLUE;
        $options = compact('glue');
        return $this->type($column, 'csv', $options);
    }

    /**
     * Type column(s) as lines of values.
     *
     * @param string|string[] $column
     * @return static
     */
    public function typeLines($column)
    {
        return $this->type($column, 'lines');
    }
    
    /**
     * Type column(s) as Ordered List.
     *
     * @param string|string[] $column
     * @return static
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
     * @return static
     */
    public function typeText($column)
    {
        return $this->type($column, 'text');
    }
    
    /**
     * Type column(s) as Unordered List.
     *
     * @param string|string[] $column
     * @return static
     */
    public function typeUL($column)
    {
        return $this->type($column, 'ul');
    }
}