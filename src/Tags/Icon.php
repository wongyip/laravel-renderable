<?php

namespace Wongyip\Laravel\Renderable\Tags;

use Wongyip\HTML\TagAbstract;

/**
 * Temp implementation.
 */
class Icon extends TagAbstract
{
    protected string $tagName = 'i';

    /**
     * @param string $iconName
     * @param bool|null $fixedWidth
     * @param string|null $iconStyle
     * @return static
     */
    public static function create(string $iconName, bool $fixedWidth = null, string $iconStyle = null): static
    {
        $styleClass = $iconStyle ? "fa-$iconStyle" : 'fas';
        $utilsClass = $fixedWidth ? 'fa-fw' : '';
        $iconClass = "fa-$iconName";
        return static::make()->class('fa', $styleClass, $iconClass, $utilsClass);
    }

    /**
     * @return array|string[]
     */
    public function addAttrs(): array
    {
        return [];
    }
}