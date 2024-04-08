<?php namespace Wongyip\Laravel\Renderable\Components;

use Exception;
use Exception as ExceptionAlias;
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
     * Whether the value is raw HTML.
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
     * Expected data-type of the value.
     *
     * @var string
     */
    public string $type;
    /**
     * Used by type: bool.
     *
     * @var string
     */
    public string $valueFalse;
    /**
     * String to replace null value for output. When used in bool columns, all
     * non-boolean values are replaced with this string.
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
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        $defaults = config('renderable.default.options');
        $defaults = is_array($defaults) ? $defaults : [];
        $options = $options ?? [];
        $this->update($options);
        foreach ($defaults as $key => $value) {
            try {
                $set = key_exists($key, $options) ? $options[$key] : null;
                if (property_exists($this, $key)) {
                    $this->$key = $set ?? $value;
                }
                else {
                    throw new Exception('Invalid option key: ' . $key);
                }
            }
            catch (Throwable $e) {
                Log::warning(sprintf('ColumnOptions.error: %s', $e->getMessage()));
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
        return new static();
    }

    /**
     * @param string|null $glue
     * @return static
     */
    public static function csv(string $glue = null): static
    {
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

    /**
     * @param array $options
     * @return static
     */
    public function update(array $options): static
    {
        try {
            foreach ($options as $key => $value) {
                if (isset($value)) {
                    if (property_exists($this, $key)) {
                        $this->$key = $value;
                    } else {
                        throw new Exception('Invalid option key: ' . $key);
                    }
                }
            }
        }
        catch (Exception $e) {
            Log::warning('ColumnOptions.update.warning: ' . $e->getMessage());
        }
        return $this;
    }
}