<?php namespace Wongyip\Laravel\Renderable;

interface RenderableInterface
{
    /**
     * Render the data-model according to the current settings, output sanitized
     * HTML ready to output in raw format (e.g. with the |raw filter of Twig).
     *
     * @return string
     */
    public function render(): string;
}