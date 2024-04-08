<?php

namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Utils\Bootstrap;

/**
 * @todo Work in progress.
 */
trait LayoutGrid
{
    /**
     * @return static
     */
    protected function layoutGrid(): static
    {
        $this->table->tagName('div')->class(['renderable-grid', 'row']);
        $this->fieldHeader->tagName('div');
        $this->valueHeader->tagName('div');
        $this->container->classRemove(Bootstrap::classTableResponsive());
        return $this;
    }
}