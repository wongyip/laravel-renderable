<?php

namespace Wongyip\Laravel\Renderable\Components;

use Wongyip\HTML\TagAbstract;

class RawHTML extends TagAbstract
{
    /**
     * @var string
     */
    private string $rawHTML = '';

    public function addAttrs(): array
    {
        return [];
    }

    /**
     * Override parent, output raw HTML directly.
     *
     * @param array|null $adHocAttrs
     * @return string
     */
    public function render(array $adHocAttrs = null): string
    {
        return $this->rawHTML;
    }

    /**
     * @param string $html
     * @return static
     */
    public static function create(string $html): static
    {
        $tag = static::make();
        $tag->rawHTML = $html;
        return $tag;
    }
}