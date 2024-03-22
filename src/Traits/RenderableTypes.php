<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Components\ColumnOptions;

trait RenderableTypes
{
    use RenderableColumnOptions;

    /**
     * @param string|array $columns
     * @param string $type
     * @param array|ColumnOptions|null $columnOptions
     * @return $this
     */
    private function __typeAll(string|array $columns, string $type, array|ColumnOptions $columnOptions = null): static
    {
        $columns = is_array($columns) ? $columns : [$columns];
        foreach ($columns as $column) {
            $this->type($column, 'boolean');
            if ($columnOptions) {
                $this->columnOptions($column, $columnOptions);
            }
        }
        return $this;
    }

    /**
     * Get or set the label of a column, getter returns smartCaps($column) if no
     * label is set.
     *
     * @param string $column
     * @param string|null $type
     * @return string|null|static
     */
    public function type(string $column, string $type = null): string|null|static
    {
        // Get
        if (is_null($type)) {
            return key_exists($column, $this->types)
                ? $this->types[$column]
                : null;
        }
        // Set
        $this->types = array_merge($this->types, [$column => $type]);
        return $this;
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
        return $this->__typeAll($columns, 'bool', ColumnOptions::bool($valueTrue, $valueFalse, $valueNull));
    }

    /**
     * Alias to typeBool()
     * @deprecated
     */
    public function typeBoolean(string|array $columns, string $valueTrue = null, string $valueFalse = null, string $valueNull = null): static
    {
        return $this->typeBool($columns, $valueTrue, $valueFalse, $valueNull);
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
        return $this->__typeAll($columns, 'csv', ColumnOptions::csv($glue));
    }

    /**
     * Type column(s) as lines of values.
     *
     * @param string|array|string[] $columns
     * @return static
     */
    public function typeLines(array|string $columns): static
    {
        return $this->__typeAll($columns, 'lines');
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
        return $this->__typeAll(
            $columns,
            'ol',
            ColumnOptions::list($listClass, $listStyle, $itemClass, $itemStyle)
        );
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
        return $this->__typeAll($columns, 'text');
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
        return $this->__typeAll(
            $columns,
            'ul',
            ColumnOptions::list($listClass, $listStyle, $itemClass, $itemStyle)
        );
    }
}