<?php namespace Wongyip\Laravel\Renderable\Traits;


trait OptionsTrait
{
    /**
     * Column specific options array.
     *
     * @var array
     */
    protected $options = [];
    
    /**
     * Get or set the options array data type of a column, where setter supports
     * an array of columns as input.
     * 
     * Note that certain setter methods, e.g. typeBoolean(), is recommended to
     * use when settting data type if there are options bound to that data type.
     *
     * Setter will merge $options into existing options array unless $replace is TRUE.
     * 
     * @param string|string[] $column
     * @param array           $options
     * @param boolean $replace
     * @return array|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function options($column, $options = null, $replace = false)
    {
        // Get
        if (is_null($options)) {
            // @todo assumed $column is string here
            return key_exists($column, $this->options) ? $this->options[$column] : [];
        }
        // Set
        $cols = is_array($column) ? $column : [$column];
        foreach ($cols as $col) {
            // Replace operation
            if ($replace) {
                $this->options[$col] = [];
            }
            // Existing
            if (key_exists($col, $this->options)) {
                // Merge, override existing. 
                $this->options[$col] = array_merge($this->options[$col], $options);
            }
            else {
                $this->options[$col] = $options;
            }
        }
        return $this;
    }
}