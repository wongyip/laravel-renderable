<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Components\ColumnOptions;

trait RenderableColumnOptions
{
    /**
     * Get or set the ColumnOptions of a column.
     *
     * @param string $column
     * @param ColumnOptions|null $options
     * @return ColumnOptions|null|static
     */
    public function columnOptions(string $column, ColumnOptions $options = null): ColumnOptions|null|static
    {
        // Get
        if (is_null($options)) {
            return key_exists($column, $this->columnsOptions)
                ? $this->columnsOptions[$column]
                : null;
        }
        // Set
        $this->columnsOptions = array_merge($this->columnsOptions, [$column => $options]);
        return $this;
    }
}