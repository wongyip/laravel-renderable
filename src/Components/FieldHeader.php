<?php namespace Wongyip\Laravel\Renderable\Components;

use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\Laravel\Renderable\Traits\HtmlRender;

/**
 * @see /views/grid.twig
 * @see /views/table.twig
 */
class FieldHeader
{
    use HtmlRender;

    /**
     * @var string
     */
    protected static string $defaultContent = 'Field';

    /**
     * @param string $layout
     * @param string|null $content
     */
    public function __construct(string $layout, string $content = null)
    {
        $this->resetTagName($layout);
        $this->content($content ?? static::$defaultContent);
    }

    /**
     * Reset to default tagName based on layout.
     *
     * @param string $layout
     * @return $this
     */
    public function resetTagName(string $layout): static
    {
        return $this->tagName($layout === Renderable::LAYOUT_TABLE ? 'th' : 'div');
    }
}