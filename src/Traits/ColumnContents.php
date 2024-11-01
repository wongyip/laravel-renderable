<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Renderable;

/**
 * Configure data-type of columns by creating or updating properties of their
 * ColumnsOptions.
 */
trait ColumnContents
{
    const CONTENT_SCROLLING_OFF = 0;
    const CONTENT_SCROLLING_AUTO = 1;
    const CONTENT_SCROLLING_X = 2;
    const CONTENT_SCROLLING_Y = 3;

    use Columns;

    /**
     * Make the column's value container scrolling long content.
     *
     * @todo Add reverse/cancel operation.
     * @todo It's not print friendly now.
     *
     * @param string $name
     * @param int $maxHeight CSS max-height, default 320 (unit pixels).
     * @param int|null $maxWidth Optional, no default.
     * @param int|null $scrolling Optional, default Renderable::CONTENT_SCROLLING_AUTO.
     * @return Renderable
     * @use Renderable::CONTENT_SCROLLING_OFF | Renderable::CONTENT_SCROLLING_AUTO | Renderable::CONTENT_SCROLLING_X | Renderable::CONTENT_SCROLLING_Y
     */
    public function scrolling(string $name, int $maxHeight = 500, int $maxWidth = null, int $scrolling = null): Renderable
    {
        $scrolling = $scrolling ?? static::CONTENT_SCROLLING_AUTO;
        $this->columnOptions($name, compact('scrolling', 'maxHeight', 'maxWidth'));
        return $this;
    }
}