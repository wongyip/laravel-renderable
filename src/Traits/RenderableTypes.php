<?php namespace Wongyip\Laravel\Renderable\Traits;

trait RenderableTypes
{
    use RenderableOptions;

    /**
     * Type of columns.
     *
     * @var array|string[]
     */
    protected array $types = [];

    /**
     * @param string|array $columns
     * @param string $type
     * @param array|null $options
     * @return $this
     */
    private function __typeAll(string|array $columns, string $type, array $options = null): static
    {
        $columns = is_array($columns) ? $columns : [$columns];
        foreach ($columns as $column) {
            $this->type($column, 'boolean');
            $this->options($column, $options);
            if ($options) {
                $this->options($column, $options);
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
    public function typeBoolean(string|array $columns, string $valueTrue = null, string $valueFalse = null, string $valueNull = null): static
    {
        $valueTrue  = is_null($valueTrue)  ? config('renderable.default.bool-true') : $valueTrue;
        $valueFalse = is_null($valueFalse) ? config('renderable.default.bool-false') : $valueFalse;
        $valueNull  = is_null($valueNull)  ? $valueFalse : $valueNull;
        return $this->__typeAll($columns, 'boolean',  compact('valueTrue', 'valueFalse', 'valueNull'));
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
        $glue = is_string($glue) ? $glue : config('renderable.default.csv-glue');
        return $this->__typeAll($columns, 'csv', compact('glue'));
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
     * @return static
     */
    public function typeOL(array|string $columns): static
    {
        return $this->__typeAll($columns, 'ol');
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
     * @return static
     */
    public function typeUL(array|string $columns): static
    {
        return $this->__typeAll($columns, 'ul');
    }
}