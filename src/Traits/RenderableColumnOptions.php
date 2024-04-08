<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Components\ColumnOptions;

trait RenderableColumnOptions
{
    /**
     * Get or set the ColumnOptions of a column.
     *
     * @param string $name
     * @param ColumnOptions|array|null $options
     * @return ColumnOptions|static
     */
    public function columnOptions(string $name, ColumnOptions|array $options = null): ColumnOptions|static
    {
        // Get
        if (is_null($options)) {
            return key_exists($name, $this->columnsOptions)
                ? $this->columnsOptions[$name]
                : new ColumnOptions();
        }
        // Set
        if ($options instanceof ColumnOptions) {
            $this->columnsOptions[$name] = $options;
        }
        else {
            // Array for update.
            if ($this->columnOptionsExists($name)) {
                $this->columnsOptions[$name]->update($options);
            }
            // Array for create.
            else {
                $this->columnsOptions[$name] = new ColumnOptions($options);
            }
        }
        return $this;
    }

    /**
     * Determine if an options is set for the given column.
     *
     * @param string $name
     * @return bool
     */
    public function columnOptionsExists(string $name) : bool
    {
        return key_exists($name, $this->columnsOptions);
    }
}