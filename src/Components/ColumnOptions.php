<?php namespace Wongyip\Laravel\Renderable\Components;

use Exception;
use Illuminate\Support\Facades\Log;
use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\Laravel\Renderable\Traits\ColumnContents;

/**
 * Customizations and options of a renderable column.
 *
 * All defaults values are defined in configuration file.
 * @see /config/renderable.php
 */
class ColumnOptions
{
    const ICON_POSITION_AFTER  = 'after';
    const ICON_POSITION_BEFORE = 'before';
    const ICON_DEFAULT_LINK    = 'link';
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
     * Name the icon, value depends on icon pack or framework.
     *
     * @var string
     */
    public string $icon;
    /**
     * Placement of icon tag if provided.
     *
     * @var string
     */
    public string $iconPosition;
    /**
     * Text caption of link column.
     * @var string|null
     */
    public string|null $linkText;
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
     * [Password only] Character that replaces the original content.
     * @var string
     */
    public string $maskChar;
    /**
     * [Password only] Number of $maskChan to be repeated.
     * @var string|null
     */
    public ?string $maskLength = null;
    /**
     * Effective on scrolling enabled.
     *
     * @var int
     */
    public int $maxHeight;
    /**
     * Effective on scrolling enabled.
     *
     * @var int
     */
    public int $maxWidth;
    /**
     * @var int
     * @see Renderable::scrolling()
     */
    public int $scrolling;
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
     * Input of null option value will be ignored.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        // Merge input (if set) into defaults.
        $defaults = config('renderable.columnOptions');
        $options = array_merge($defaults, $options ?? []);
        foreach ($options as $prop => $set) {
            if (property_exists($this, $prop)) {
                $this->$prop = $set ?? $defaults[$prop];
            }
            else {
                Log::warning(sprintf('ColumnOptions: property %s does not exists.', $prop));
            }
        }
    }

    /**
     * Make a ColumnOptions with for Boolean column.
     *
     * @param string|null $valueTrue
     * @param string|null $valueFalse
     * @param string|null $valueNull
     * @return static
     */
    public static function bool(string $valueTrue = null, string $valueFalse = null, string $valueNull = null): static
    {
        return new static(compact('valueTrue', 'valueFalse', 'valueNull'));
    }

    /**
     * Make a ColumnOptions with all default/preset options.
     *
     * @return static
     */
    public static function defaults(): static
    {
        return new static();
    }

    /**
     * Make a ColumnOptions with for CSV column.
     *
     * @param string|null $glue
     * @return static
     */
    public static function csv(string $glue = null): static
    {
        return new static(compact('glue'));
    }

    /**
     * Make a ColumnOptions with for a list column.
     *
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
     * Batch update of options.
     *
     * @param array $options
     * @return static
     */
    public function update(array $options): static
    {
        try {
            foreach ($options as $key => $set) {
                if (isset($set)) {
                    if (property_exists($this, $key)) {
                        $this->$key = $set;
                    }
                    else {
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