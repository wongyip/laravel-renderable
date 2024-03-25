<?php namespace Wongyip\Laravel\Renderable\Components;

use Wongyip\Laravel\Renderable\Traits\HtmlRender;

/**
 * The HTML tag wrapping the Renderable object (layout-level).
 *
 * @see /views/renderable.twig
 */
class Container
{
    // New
    use HtmlRender;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->classAppend('renderable-object-container');
        $this->id($id);
    }
}