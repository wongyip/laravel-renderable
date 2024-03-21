<?php namespace Wongyip\Laravel\Renderable;

use Wongyip\Laravel\Renderable\Components\ColumnRenderable;

interface RenderableInterface
{
    /**
     * Get single attribute value by column name. (NO SETTER)
     *
     * @param string $column
     * @return mixed|null
     */
    public function attribute(string $column): mixed;

    /**
     * Get or set attributes for rendering.
     *
     * @param array|null $attributes
     * @return array|static
     */
    public function attributes(array $attributes = null): array|static;
    
    /**
     * Get or set columns to be rendered as plain text.
     *
     * Setter:
     *  1. Take all keys in $this->attributes if $columns is TRUE.
     *  2. Merge into existing $this->columns unless $replace is TRUE.
     *  3. N.B. Getter respect excluded columns.
     *
     * @param string|array|string[]|bool|null $columns
     * @param bool $replace
     * @return array|string[]|static
     */
    public function columns(string|array|bool $columns = null, bool $replace = false): array|static;
    
    /**
     * Get or set columns to be rendered as HTML.
     *
     * Setter:
     *  1. Take all keys in $this->attributes if $columns is TRUE.
     *  2. Merge into existing $this->columnsHTML unless $replace is TRUE.
     *  3. N.B. Getter respect excluded columns.
     *
     * @param string|array|string[]|bool|null $columns
     * @param bool $replace
     * @return array|string[]|static
     */
    public function columnsHTML(string|array|bool $columns = null, bool $replace = false): array|static;
    
    /**
     * Get or set columns that should be excluded from rendering.
     *
     * Setter:
     *  1. Merge into existing $this->excluded unless $replace is TRUE.
     *  2. To clear $this->excluded, input an empty array for $excluded and TRUE for $replace.
     *
     * @param string|array|string[] $columns
     * @param bool $replace
     * @return array|string[]|static
     */
    public function exclude(string|array $columns = null, bool $replace = false): array|static;
    
    /**
     * Get or set the layout.
     *
     * @param string|null $layout
     * @return string|static
     */
    public function layout(string $layout = null): string|static;
    
    /**
     * Get or set the options array of a column, where setter supports
     * an array of columns as input.
     *
     * Note that certain setter methods, e.g. typebool(), is recommended to
     * use when setting data type if there are options bound to that data type.
     *
     * Setter will merge $options into existing options array unless $replace is TRUE.
     *
     * @param string $column
     * @param string|null $type
     * @return array|null|static
     */
    public function options(string $column, string $type = null): array|null|static;
    
    /**
     * Get an array of ColumnRenderable objects compiled base on the current
     * state of the Renderable class.
     *
     * @return array|ColumnRenderable[]
     */
    public function renderables(): array;
    
    /**
     * Get or set data type of column, where setter support an array of columns
     * as input.
     *
     * @param string $column
     * @param string|null $type
     * @return string|null|static
     */
    public function type(string $column, string $type = null): string|null|static;

    /**
     * Get the view with the renderable marco, e.g. 'renderable::table'.
     *
     * @return string
     */
    public function view(): string;
}