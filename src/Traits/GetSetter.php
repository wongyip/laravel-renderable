<?php

namespace Wongyip\Laravel\Renderable\Traits;

trait GetSetter
{
    /**
     * Generic Get/Setter Method.
     *
     * Notes: 1. for non-exist property, getter return NULL, and setter do
     * nothing and return static. 2. Setter cannot set property to NULL, do
     * it yourself...
     *
     * @param string $property
     * @param mixed|null $setter
     * @return mixed|static
     */
    protected function getSet(string $property, $setter = null)
    {
        $property = preg_replace("/(^.*::)/", '', $property);
        if (property_exists($this, $property)) {
            if (isset($setter)) {
                $this->$property = $setter;
                return $this;
            }
            return $this->$property ?? null;
        }
        return isset($setter) ? $this : null;
    }
}