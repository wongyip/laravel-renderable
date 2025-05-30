<?php

namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Support\Facades\Log;
use Wongyip\Laravel\Renderable\Components\ColumnOptions;

trait Columns
{
    /**
     * Columns to be rendered in raw HTML.
     *
     * @var array|string[]
     */
    protected array $columnsHTML = [];
    /**
     * Renderable options of columns.
     *
     * @var array|ColumnOptions[]
     */
    protected array $columnsOptions = [];
    /**
     * Columns NOT to be rendered.
     *
     * @var array|string[]
     */
    protected array $excluded = [];
    /**
     * Columns to be rendered, unless specified in $this->excluded.
     *
     * @note Either array or TRUE.
     * @var array|string[]|bool
     */
    protected array|bool $included = [];

    /**
     * @deprecated Replaced by include() method.
     * @see self::include()
     */
    public function columns(array|string|bool $names = null, bool $replace = false): array|static
    {
        return $this->include($names, $replace);
    }

    /**
     * Get or set columns to be rendered as HTML.
     *
     * Setter:
     *  1. Take all keys in $this->attributes if $columns is TRUE.
     *  2. Merge into existing $this->columnsHTML unless $replace is TRUE.
     *  3. Getter respect $excluded.
     *
     * @param string|array|string[]|bool|null $names
     * @param bool $replace
     * @return array|string[]|static
     */
    public function columnsHTML(string|array|bool $names = null, bool $replace = false): array|static
    {
        // Get
        if (is_null($names)) {
            return array_diff($this->columnsHTML, $this->excluded);
        }
        // Set
        $this->columnsHTML = $replace ? $names : array_merge($this->columnsHTML, $names);
        return $this;
    }
    /**
     * Get or set the ColumnOptions of a column. Input ColumnOptions object to
     * replace existing, or array of options to update existing (create on
     * absent).
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

    /**
     * Get or set columns that should be excluded from rendering.
     *
     * Setter:
     *  1. Merge into existing $this->excluded unless $replace is TRUE.
     *  2. Input an empty array for $excluded and TRUE for $replace to empty $excluded.
     *
     * @param string|array|string[]|null $names
     * @param bool $replace
     * @return array|string[]|static
     */
    public function exclude(string|array $names = null, bool $replace = false): array|static
    {
        // Get
        if (is_null($names)) {
            return $this->excluded;
        }
        // Set
        $names = is_array($names) ? $names : [$names];
        $this->excluded = array_unique($replace ? $names : array_merge($this->excluded, $names));
        return $this;
    }

    /**
     * Get or set columns to be rendered by names.
     *
     * Getter:
     *  - Ignores $replace argument.
     *  - Respect $excluded property.
     *
     * Setter:
     *  - Input TRUE to include all columns, ignore $replace argument.
     *  - Input FALSE to clear the list, ignore $replace argument.
     *  - In case of array is given, only scalars (except boolean) are taken.
     *  - Merge input into the list unless $replace is TRUE.
     *
     * @param string|array|string[]|bool|null $names
     * @param bool $replace
     * @return array|string[]|static
     */
    public function include(array|string|bool $names = null, bool $replace = false): array|static
    {
        // Get
        if (is_null($names)) {
            // Evaluate the attribute names actually included.
            $included = is_array($this->included)
                ? $this->included
                : ($this->included ? array_keys($this->attributes) : []);
            // Blame
            if ($denied = array_intersect($included, $this->excluded)) {
                Log::debug(sprintf('Renderable.include() attribute(s) [%s] in both $included & $excluded are EXCLUDED.', implode(', ', $denied)));
            }
            return array_unique(array_diff($included, $this->excluded));
        }
        // Set
        if (is_bool($names)) {
            // TRUE denotes all, while FALSE means clear.
            $this->included = $names ?: [];
        }
        else {
            if ($this->included === true) {
                Log::debug('Renderable.include() already including all columns.');
            }
            else {
                // Array
                if (is_array($names)) {
                    $names = array_filter($names, function ($name) {
                        return (is_scalar($name) && !is_bool($name));
                    });
                } // String
                else {
                    $names = [$names];
                }
                $this->included = $replace ? $names : array_merge($this->included, $names);
            }
        }
        return $this;
    }

    /**
     * Include a column and place it after a specific column.
     *
     * IMPORTANT: effective on selective inclusion mode only, do nothing in case
     * of included is TRUE (for all columns).
     *
     * Note: if the $afterColumn does not exists, append $column to the end of
     * the list by default, or do nothing when $strict is TRUE.
     *
     * @param string $column
     * @param string $afterColumn
     * @param bool $strict
     * @return static
     */
    public function insert(string $column, string $afterColumn, bool $strict = false): static
    {
        if (is_array($this->included)) {

            $indexColumn = array_search($column, $this->included);
            $indexReference = array_search($afterColumn, $this->included);

            // Reference column exists.
            if ($indexReference !== false) {

                // Column included already: remove column and insert again after the reference column.
                if ($indexColumn !== false) {
                    $this->included[$indexColumn] = null;
                    array_splice($this->included, $indexReference + 1, 0, $column);
                    $this->included = array_filter($this->included);
                }
                // Simply insert.
                else {
                    array_splice($this->included, $indexReference + 1, 0, $column);
                }
            }
            else {
                // Both not exist: append column when not in $strict mode.
                if ($indexColumn === false && !$strict) {
                    $this->include($column);
                }
                /**
                 * @note Do nothing if column is already included and the
                 * reference column does not exists.
                 */
            }
        }
        return $this;
    }

    /**
     * Include a column and place it before a specific column.
     *
     * IMPORTANT: effective on selective inclusion mode only, do nothing in case
     * of included is TRUE (for all columns).
     *
     * Note: if the $beforeColumn does not exists, append $column to the end of
     * the list by default, or do nothing when $strict is TRUE.
     *
     * @param string $column
     * @param string $beforeColumn
     * @param bool $strict
     * @return static
     */
    public function insertBefore(string $column, string $beforeColumn, bool $strict = false): static
    {
        if (is_array($this->included)) {

            $indexColumn = array_search($column, $this->included);
            $indexReference = array_search($beforeColumn, $this->included);

            // Reference column exists.
            if ($indexReference !== false) {

                // Column included already: remove column and insert again after the reference column.
                if ($indexColumn !== false) {
                    $this->included[$indexColumn] = null;
                    array_splice($this->included, $indexReference, 0, $column);
                    $this->included = array_filter($this->included);
                }
                // Simply insert.
                else {
                    array_splice($this->included, $indexReference, 0, $column);
                }
            }
            else {
                // Both not exist: append column when not in $strict mode.
                if ($indexColumn === false && !$strict) {
                    $this->include($column);
                }
                /**
                 * @note Do nothing if column is already included and the
                 * reference column does not exists.
                 */
            }
        }
        return $this;
    }
}