<?php namespace Wongyip\Laravel\Renderable\Components;

use Illuminate\Support\Facades\Log;

/**
 * Options and switches of the Renderable object.
 *
 * All defaults values are defined in configuration file.
 * @see /config/renderable.php
 */
class RenderableOptions
{
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
     */
    public string $fieldHeader;
    /**
     * The ID Prefix for ALL generated tags having ID attribute.
     *
     * @var string
     */
    public string $idPrefix;
    /**
     * String prepended to the contents HTML.
     *
     * @var string
     */
    public string $prefix;
    /**
     * Effective for table layout only.
     *
     * @var bool
     */
    public bool $renderTableHead;
    /**
     * String appended to the contents HTML.
     *
     * @var string
     */
    public string $suffix;
    /**
     * Table's caption CSS 'caption-side: bottom|inherit|initial|revert|revert-layer|top|unset'.
     *
     * @var string
     */
    public string $tableCaptionSide;
    /**
     * Header of the "Value" column.
     *
     * @var string
     */
    public string $valueHeader;

    /**
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        // Merge input (if set) into defaults.
        $defaults = config('renderable.options');
        $options = array_merge($defaults, $options ?? []);
        foreach ($options as $prop => $set) {
            if (property_exists($this, $prop)) {
                $this->$prop = $set ?? $defaults[$prop];
            }
            else {
                Log::warning(sprintf('RenderableOptions: property %s does not exists.', $prop));
            }
        }
    }
}