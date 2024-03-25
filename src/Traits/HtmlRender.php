<?php

namespace Wongyip\Laravel\Renderable\Traits;

use Spatie\HtmlElement\HtmlElement;

trait HtmlRender
{
    use HtmlAttributes;

    /**
     * @return string
     */
    public function render(): string
    {
        $attrNames = ['class', 'id', 'style'];
        $attributes = [];
        foreach ($attrNames as $getter) {
            if (method_exists($this, $getter)) {
                if ($val = $this->$getter()) {
                    $attributes[$getter] = $val;
                }
            }
        }
        return HtmlElement::render($this->tagName(), $attributes, $this->content);
    }
}