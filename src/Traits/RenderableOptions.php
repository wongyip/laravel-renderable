<?php namespace Wongyip\Laravel\Renderable\Traits;

trait RenderableOptions
{
    /**
     * Renderable options of columns.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Get or set the options of a column.
     *
     * @param string $column
     * @param string|null $type
     * @return array|null|static
     */
    public function options(string $column, string $type = null): array|null|static
    {
        // Get
        if (is_null($type)) {
            return key_exists($column, $this->options)
                ? $this->options[$column]
                : null;
        }
        // Set
        $this->options = array_merge($this->options, [$column => $type]);
        return $this;
    }
}