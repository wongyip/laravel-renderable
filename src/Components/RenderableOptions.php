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
     * @var string
     */
    public string $emptyRecord;
    /**
     * @var string
     */
    public string $fieldHeader;
    /**
     * Effective for table layout only.
     *
     * @var bool
     */
    public bool $renderTableHead;
    /**
     * Table's caption CSS 'caption-side: bottom|inherit|initial|revert|revert-layer|top|unset'.
     *
     * @var string
     */
    public string $tableCaptionSide;
    /**
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