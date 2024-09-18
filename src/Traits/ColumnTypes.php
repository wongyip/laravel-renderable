<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Components\ColumnOptions;

/**
 * Configure data-type of columns by creating or updating properties of their
 * ColumnsOptions.
 */
trait ColumnTypes
{
    use Columns;

    /**
     * Set or update type and related options of the given column(s).
     * N.B. $type take precedence over $columnOptions->type or $columnOptions['type'].
     *
     * @param string|array $names
     * @param string $type
     * @param array|ColumnOptions|null $options
     * @return $this
     */
    private function __typeColumns(string|array $names, string $type, array|ColumnOptions $options = null): static
    {
        foreach ((is_array($names) ? $names : [$names]) as $column) {
            $this->type($column, $type, $options);
        }
        return $this;
    }

    /**
     * Set or update type and related options of a single column.
     * N.B. $type take precedence over $columnOptions->type or $columnOptions['type'].
     *
     * @param string $name
     * @param string|null $type
     * @param array|ColumnOptions|null $options
     * @return string|null|static
     */
    public function type(string $name, string $type = null, array|ColumnOptions $options = null): string|null|static
    {
        // Get
        if (is_null($type)) {
            // A default type is returned if options is not defined.
            return $this->columnOptions($name)->type;
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
        return $this->columnOptions($name, $options);
    }

    /**
     * Type column(s) as Boolean.
     *
     * @param string|array|string[] $names
     * @param string|null $valueTrue Default 'Yes'
     * @param string|null $valueFalse Default 'No'
     * @param string|null $valueNull Default to $valueFalse
     * @return static
     */
    public function typeBool(string|array $names, string $valueTrue = null, string $valueFalse = null, string $valueNull = null): static
    {
        return $this->__typeColumns($names, 'bool', compact('valueTrue', 'valueFalse', 'valueNull'));
    }

    /**
     * Type column(s) as CSV.
     *
     * @param string|string[]|array $names
     * @param string|null $glue
     * @return static
     */
    public function typeCSV(array|string $names, string $glue = null): static
    {
        return $this->__typeColumns($names, 'csv', compact('glue'));
    }

    /**
     * Type column(s) as HTML code.
     *
     * @param string|array|string[] $names
     * @return static
     */
    public function typeHTML(array|string $names): static
    {
        return $this->__typeColumns($names, 'html');
    }

    /**
     * Type column(s) as lines of values.
     *
     * @param string|array|string[] $names
     * @return static
     */
    public function typeLines(array|string $names): static
    {
        return $this->__typeColumns($names, 'lines');
    }

    /**
     * Type column(s) as link.
     *
     * @param string|array|string[] $names
     * @param string|bool|null $icon Default null for no icon, set TRUE for default icon, string for icon with matching name.
     * @param bool|null $iconAfterLink Placement of the icon.
     * @return static
     * @see ColumnOptions::ICON_POSITION_AFTER
     * @see ColumnOptions::ICON_POSITION_BEFORE
     *
     */
    public function typeLink(array|string $names, string|bool $icon = null, bool $iconAfterLink = null): static
    {
        $icon = $icon ? ($icon === true ? ColumnOptions::ICON_DEFAULT_LINK : $icon) : '';
        $iconPosition = $iconAfterLink
            ? ColumnOptions::ICON_POSITION_AFTER
            : ColumnOptions::ICON_POSITION_BEFORE;
        return $this->__typeColumns($names, 'link', compact('icon', 'iconPosition'));
    }

    /**
     * Type column(s) as Ordered List.
     *
     * @param string|array|string[] $names
     * @param string|null $listClass
     * @param string|null $listStyle
     * @param string|null $itemClass
     * @param string|null $itemStyle
     * @return static
     */
    public function typeOL(array|string $names, string $listClass = null, string $listStyle = null, string $itemClass = null, string $itemStyle = null): static
    {
        return $this->__typeColumns($names, 'ol', compact('listClass', 'listStyle', 'itemClass', 'itemStyle'));
    }

    /**
     * N.B. People usually want 'text' instead of 'string', where text will
     * be processed with nl2br() and 'string' will not.
     *
     * Type column(s) as string, which will be rendered normally as innerText,
     * which line break is not rendered and looks like a space.
     *
     * @param string|array|string[] $names
     * @return static
     */
    public function typeString(array|string $names): static
    {
        return $this->__typeColumns($names, 'string');
    }

    /**
     * Type column(s) as multi-line text, which will be processed with nl2br()
     * and rendered as innerHTML of the value tag.
     *
     * @param string|array|string[] $names
     * @return static
     */
    public function typeText(array|string $names): static
    {
        return $this->__typeColumns($names, 'text');
    }

    /**
     * Type column(s) as Unordered List.
     *
     * @param string|array|string[] $names
     * @param string|null $listClass
     * @param string|null $listStyle
     * @param string|null $itemClass
     * @param string|null $itemStyle
     * @return static
     */
    public function typeUL(array|string $names, string $listClass = null, string $listStyle = null, string $itemClass = null, string $itemStyle = null): static
    {
        return $this->__typeColumns($names, 'ul', compact('listClass', 'listStyle', 'itemClass', 'itemStyle'));
    }
}