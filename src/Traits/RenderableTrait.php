<?php namespace Wongyip\Laravel\Renderable\Traits;


trait RenderableTrait
{   
    /**
     * @return string
     */
    public function view()
    {
        return $this->_viewPrefix . $this->layout();
    }
    
    /**
     * Get or set a "columns" property.
     *
     * @param string          $property
     * @param string[]|string $columns
     * @param boolean         $replace
     * @return string[]|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    protected function getSetColumnsProp($property, $columns = null, $replace = false)
    {
        // Get
        if (is_null($columns)) {
            return $this->$property;
        }
        // Set
        $columns = is_array($columns) ? $columns : [$columns];
        $this->$property = $replace ? $columns : array_merge($this->$property, $columns);
        return $this;
    }
    
    /**
     * Get or set a property.
     *
     * @param string $property
     * @param mixed  $value
     * @return mixed|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    protected function getSetProp($property, $value = null)
    {
        // Get
        if (is_null($value)) {
            return $this->$property;
        }
        // Set
        $this->$property = $value;
        return $this;
    }
    
    /**
     * Get or set a property that is an assoc. array.
     *
     * @param string $property
     * @param mixed  $value
     * @return mixed|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    protected function getSetPropAssoc($property, $key, $value)
    {
        // Get
        if (is_null($value)) {
            if (key_exists($key, $this->$property)) {
                return $this->$property[$key];
            }
            return null;
        }
        // Set
        $this->$property[$key] = $value;
        return $this;
    }
}