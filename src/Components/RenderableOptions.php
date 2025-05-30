<?php namespace Wongyip\Laravel\Renderable\Components;

use Illuminate\Support\Facades\Log;
use Wongyip\HTML\Interfaces\RendererInterface;
use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\Laravel\Renderable\Traits\ColumnHeaders;

/**
 * Options and switches of the Renderable object.
 *
 * All defaults values are defined in configuration file.
 * @see /config/renderable.php
 */
class RenderableOptions
{
    /**
     * Whether the output HTML should be formatted.
     *
     * @var bool
     */
    public bool $beautifyHTML;
    /**
     * Suffix to the wrapper container's ID (HTML tag attribute).
     *
     * @var string
     */
    public string $containerIdSuffix;
    /**
     * Message on empty input of attributes.
     *
     * @var string
     */
    public string $emptyRecord;
    /**
     * Header of the "Field" column.
     *
     * @var string
     * @deprecated
     * @see ColumnHeaders::$columnHeaders
     */
    public string $fieldHeader;
    /**
     * Grid's CSS class(s) append after Renderable::CSS_CLASS_TABLE.
     *
     * @var string
     */
    public string $gridClassAppend;
    /**
     * Grid's CSS class(s) prepend before Renderable::CSS_CLASS_TABLE.
     *
     * @var string
     */
    public string $gridClassPrepend;
    /**
     * The ID Prefix for ALL generated tags having ID attribute.
     *
     * @var string
     */
    public string $idPrefix;
    /**
     * String prepended to the contents HTML.
     *
     * @var string|RendererInterface
     */
    public string|RendererInterface $prefix;
    /**
     * Effective for vertical table layout only.
     *
     * @var bool
     * @deprecated
     * @see ColumnHeaders::$columnHeaders
     */
    public bool $renderTableHead;
    /**
     * String appended to the contents HTML.
     *
     * @var string|RendererInterface
     */
    public string|RendererInterface $suffix;
    /**
     * Table's caption CSS 'caption-side: bottom|inherit|initial|revert|revert-layer|top|unset'.
     *
     * @var string
     */
    public string $tableCaptionSide;
    /**
     * Table's CSS class(s) appended to the class list on render.
     *
     * @var string
     */
    public string $tableClassAppend;
    /**
     * Base css class of table renderable.
     *
     * @var string
     */
    public string $tableClassBase;
    /**
     * Add 'table-bordered' class, before $tableClassAppend.
     *
     * @var bool
     */
    public bool $tableBordered = true;
    /**
     * Add 'table-borderless' class, before $tableClassAppend.
     * Note: Ignored when $tableBordered is TRUE.
     *
     * @var bool
     */
    public bool $tableBorderless = true;
    /**
     * Table's CSS class(s) prepended on render.
     *
     * @var string
     */
    public string $tableClassPrepend;
    /**
     * Whether fields are rendered horizontally in table layout.
     *
     * @var bool
     * @note This is NOT accessible via the parent Renderable::class.
     * @see Renderable::tableHorizontal()
     */
    public bool $tableHorizontal;
    /**
     * Whether "Field" and "Value" header cells are rendered in table layout.
     *
     * @var bool
     */
    public bool $tableHorizontalHeaders;
    /**
     * Add 'table-hover' class, before $tableClassAppend.
     *
     * @var bool
     */
    public bool $tableHover = false;
    /**
     * CSS width of 'tbody > tr > td:first-child', integer value will be
     * suffixed with default unit 'px' on output.
     *
     * @var string
     */
    public string $tableLabelCellWidth;
    /**
     * Add 'table-striped' class, before $tableClassAppend.
     *
     * @var bool
     */
    public bool $tableStriped = false;
    /**
     * @var string
     * @deprecated
     * @see ColumnHeaders::$columnHeaders
     */
    public string $valueHeader;

    /**
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        // Merge input $options into defaults, and apply if matching property exists.
        $defaults = config('renderable.options');
        $options = array_merge($defaults, $options ?? []);
        foreach ($options as $prop => $set) {
            if (property_exists($this, $prop)) {
                $this->$prop = $set ?? $defaults[$prop];
            }
            else {
                Log::warning(sprintf('RenderableOptions.%s does not exists.', $prop));
            }
        }
    }
}