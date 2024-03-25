<?php namespace Wongyip\Laravel\Renderable\Components;

use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\Laravel\Renderable\Traits\HtmlRender;

/**
 * @see /views/grid.twig
 * @see /views/table.twig
 */
class ValueHeader extends FieldHeader
{
    protected static string $defaultContent = 'Value';
}