<?php namespace Wongyip\Laravel\Renderable\Traits;









// Migrate





trait GetSetters
{
    /**
     * Get or set an array-type property's value. Setter default merge into
     * existing, set $replace to TURE for replacing all existing values.
     *
     * @param string $property
     * @param array|null $array $array
     * @param bool $replace
     * @return array|null|static
     */
    protected function __merge(string $property, array $array = null, bool $replace = false): mixed
    {
        // Get
        if (is_null($array)) {
            return property_exists($this, $property) ? $this->$property : null;
        }
        // Set
        $this->$property = $replace ? $array : array_merge($this->$property, $array);
        return $this;
    }

    /**
     * Get or set a property's value.
     *
     * @param string $property
     * @param mixed|null $value
     * @return mixed|null|static
     */
    protected function __getSetProperty(string $property, mixed $value = null): mixed
    {
        // Get
        if (is_null($value)) {
            return property_exists($this, $property)
                ? $this->$property
                : null;
        }
        // Set
        $this->$property = $value;
        return $this;
    }
}