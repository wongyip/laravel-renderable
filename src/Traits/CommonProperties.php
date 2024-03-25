<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\PHPHelpers\CSS;

/**
 * Simple, strict-forward options, properties, etc., for all layouts.
 *
 * @see /views/grid.twig
 * @see /views/table.twig
 */
trait CommonProperties
{
    /**
     * @var string
     */
    public string $captionField = 'Field';
    /**
     * @var string
     */
    public string $captionValue = 'Value';
    /**
     * @var string
     */
    public string $fieldClass = '';
    /**
     * @var string
     */
    public string $fieldHeaderClass = '';
    /**
     * @var string
     */
    public string $fieldHeaderStyle = '';
    /**
     * @var string
     */
    public string $fieldStyle = '';
    /**
     * @var string
     */
    public string $labelClass = '';
    /**
     * @var string
     */
    public string $labelStyle = '';
    /**
     * @var string
     */
    public string $valueClass = '';
    /**
     * @var string
     */
    public string $valueHeaderClass = '';
    /**
     * @var string
     */
    public string $valueHeaderStyle = '';
    /**
     * @var string
     */
    public string $valueStyle = '';
}