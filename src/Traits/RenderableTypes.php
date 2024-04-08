<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Components\ColumnOptions;

trait RenderableTypes
{
    use RenderableColumnOptions;

    /**
     * Set or update type and related options of multiple columns.
     * N.B. $type take precedence over $columnOptions->type or $columnOptions['type'].
     *
     * @param string|array $columns
     * @param string $type
     * @param array|ColumnOptions|null $options
     * @return $this
     */
    private function __typeColumns(string|array $columns, string $type, array|ColumnOptions $options = null): static
    {
        foreach ((is_array($columns) ? $columns : [$columns]) as $column) {
            $this->type($column, $type, $options);
        }
        return $this;
    }

    /**
     * Set or update type and related options of a single column.
     * N.B. $type take precedence over $columnOptions->type or $columnOptions['type'].
     *
     * @param string $column
     * @param string|null $type
     * @param array|ColumnOptions|null $options
     * @return string|null|static
     */
    public function type(string $column, string $type = null, array|ColumnOptions $options = null): string|null|static
    {
        // Get
        if (is_null($type)) {
            // A default type is returned if options is not defined.
            return $this->columnOptions($column)->type;
        }
        // Set
        if ($options instanceof ColumnOptions) {
            $options->type = $type;
        }
        elseif (is_array($options)) {
            $options['type'] = $type;
        }
        else {
            $options = compact('type');
        }
        return $this->columnOptions($column, $options);
    }

    /**
     * Type column(s) as Boolean.
     *
     * @param string|array|string[] $columns
     * @param string|null $valueTrue Default 'Yes'
     * @param string|null $valueFalse Default 'No'
     * @param string|null $valueNull Default to $valueFalse
     * @return static
     */
    public function typeBool(string|array $columns, string $valueTrue = null, string $valueFalse = null, string $valueNull = null): static
    {
        return $this->__typeColumns($columns, 'bool', compact('valueTrue', 'valueFalse', 'valueNull'));
    }

    /**
     * Type column(s) as CSV.
     *
     * @param string|string[]|array $columns
     * @param string|null $glue
     * @return static
     */
    public function typeCSV(array|string $columns, string $glue = null): static
    {
        return $this->__typeColumns($columns, 'csv', compact('glue'));
    }

    /**
     * Type column(s) as lines of values.
     *
     * @param string|array|string[] $columns
     * @return static
     */
    public function typeLines(array|string $columns): static
    {
        return $this->__typeColumns($columns, 'lines');
    }

    /**
     * Type column(s) as Ordered List.
     *
     * @param string|array|string[] $columns
     * @param string|null $listClass
     * @param string|null $listStyle
     * @param string|null $itemClass
     * @param string|null $itemStyle
     * @return static
     */
    public function typeOL(array|string $columns, string $listClass = null, string $listStyle = null, string $itemClass = null, string $itemStyle = null): static
    {
        return $this->__typeColumns($columns, 'ol', compact('listClass', 'listStyle', 'itemClass', 'itemStyle'));
    }
    
    /**
     * Type column(s) as multi-line text, which will be rendered with |nl2br filter.
     * 
     * Note: no effect if column is declared as HTML.
     *
     * @param string|array|string[] $columns
     * @return static
     */
    public function typeText(array|string $columns): static
    {
        return $this->__typeColumns($columns, 'text');
    }

    /**
     * Type column(s) as Unordered List.
     *
     * @param string|array|string[] $columns
     * @param string|null $listClass
     * @param string|null $listStyle
     * @param string|null $itemClass
     * @param string|null $itemStyle
     * @return static
     */
    public function typeUL(array|string $columns, string $listClass = null, string $listStyle = null, string $itemClass = null, string $itemStyle = null): static
    {
        return $this->__typeColumns($columns, 'ul', compact('listClass', 'listStyle', 'itemClass', 'itemStyle'));
    }
}