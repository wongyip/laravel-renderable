<?php namespace Wongyip\Laravel\Renderable\Components;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Advanced options for rendering a column.
 */
class ColumnOptions
{
    /**
     * Used by type: csv.
     *
     * @var string
     */
    public string $glue;
    /**
     *
     *
     * @var bool
     */
    public bool $html;
    /**
     * Used by type: ol, ul.
     *
     * @var string
     */
    public string $itemClass;
    /**
     * Used by type: ol, ul.
     *
     * @var string
     */
    public string $itemStyle;
    /**
     * Used by type: ol, ul.
     *
     * @var string
     */
    public string $listClass;
    /**
     * Used by type: ol, ul.
     *
     * @var string
     */
    public string $listStyle;
    /**
     * Used by type: bool.
     *
     * @var string
     */
    public string $valueFalse;
    /**
     * Used by type: bool.
     *
     * @var string
     */
    public string $valueNull;
    /**
     * Used by type: bool.
     *
     * @var string
     */
    public string $valueTrue;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        foreach ($options as $key => $value) {
            try {
                if (!is_null($value)) {
                    $this->$key = $value;
                }
            }
            catch (Throwable $e) {
                Log::warning(sprintf('ColumnOptions: Unable to set property, possibly type mismatch, error/exception: %s', $e->getMessage()));
            }
        }
    }

    /**
     * @param string|null $valueTrue
     * @param string|null $valueFalse
     * @param string|null $valueNull
     * @return static
     */
    public static function bool(string $valueTrue = null, string $valueFalse = null, string $valueNull = null): static
    {
        $valueTrue  = is_null($valueTrue)  ? config('renderable.default.bool-true') : $valueTrue;
        $valueFalse = is_null($valueFalse) ? config('renderable.default.bool-false') : $valueFalse;
        $valueNull  = is_null($valueNull)  ? $valueFalse : $valueNull;
        return new static(compact('valueTrue', 'valueFalse', 'valueNull'));
    }

    /**
     * Get a ColumnOptions object with all default/preset options.
     *
     * @return static
     */
    public static function defaults(): static
    {
        return new ColumnOptions([
            'glue'       => ', ',
            'valueTrue'  => 'Yes',
            'valueFalse' => 'No',
            'valueNull'  => '',
        ]);
    }

    /**
     * @param string|null $glue
     * @return static
     */
    public static function csv(string $glue = null): static
    {
        $glue = is_string($glue) ? $glue : config('renderable.default.csv-glue');
        return new static(compact('glue'));
    }

    /**
     * @param string|null $listClass
     * @param string|null $listStyle
     * @param string|null $itemClass
     * @param string|null $itemStyle
     * @return static
     */
    public static function list(string $listClass = null, string $listStyle = null, string $itemClass = null, string $itemStyle = null): static
    {
        return new static(compact('listClass', 'listStyle', 'itemClass', 'itemStyle'));
    }
}