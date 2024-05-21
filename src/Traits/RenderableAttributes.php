<?php

namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Database\Eloquent\Model;

trait RenderableAttributes
{
    /**
     * Associative array of attribute values. Note that if an Eloquent model is
     * provided, the initial attributes will be the results of the model's
     * toArray() method. hidden model attributes and not appended accessors are
     * not included.
     *
     * @var array
     */
    protected array $attributes = [];
    /**
     * Eloquent model provide on instantiate.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Get or set a single attribute value by name.
     *
     * Getter looks for value in the $attributes property at first, then try the
     * getAttribute() method of the model (if there is one) for hidden attribute,
     * or not appended accessors/mutators.
     *
     * @param string $name
     * @param mixed $value
     * @return mixed|null|static
     */
    public function attribute(string $name, mixed $value = null): mixed
    {
        // Get
        if (is_null($value)) {
            if (key_exists($name, $this->attributes)) {
                return $this->attributes[$name];
            }
            // Maybe an accessor, which is not in the model's $appends list.
            if (isset($this->model)) {
                return $this->model->getAttribute($name) ?? null;
            }
            return null;
        }
        // Set
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Get or set attributes for rendering. Since input are merged into existing
     * attributes array, additional attributes may be added after instantiation
     * with this setter, unless $replace is TRUE.
     *
     * @param array|null $attributes
     * @param bool $replace
     * @return array|static
     */
    public function attributes(array $attributes = null, bool $replace = false): array|static
    {
        if (is_null($attributes)) {
            return $this->attributes;
        }
        $this->attributes = $replace
            ? $attributes
            : array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Retrieve the Eloquent model, if it is provided on instantiate. No setter,
     * because of, 1. it is not logical to switch to another model if you are
     * going to render the current model at the beginning, 2. changing the model
     * might affect other already-set properties or options, which make it vary
     * hard to maintain entire source code.
     *
     * @return Model|null|static
     */
    public function model(): Model|null|static
    {
        return $this->model ?? null;
    }
}