<?php

namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Support\Facades\Log;
use Wongyip\HTML\Interfaces\RendererInterface;
use Wongyip\Laravel\Renderable\Components\RenderableOptions;
use Wongyip\Laravel\Renderable\Renderable;

trait ColumnHeaders
{
    /**
     * @var array|string[]|RendererInterface[]
     */
    protected array $columnHeaders = [
        'Field',
        'Value',
    ];

    /**
     * Insert FALSE in first argument to disable (skip rendering) of the THead.
     * Insert on string or RendererInterface to render a THead with one TH cell
     * spanning across two columns.
     *
     * N.B. Applied to VERTICAL TABLE only.
     *
     * @param bool|string|RendererInterface ...$headers
     * @return array|string[]|RendererInterface[]|Renderable|static
     */
    public function columnHeaders(bool|string|RendererInterface...$headers): array|Renderable|static
    {
        if (empty($headers)) {
            return $this->columnHeaders;
        }

        if ($headers[0] === false) {
            $this->columnHeaders = [];
        }
        else {
            $headers = array_filter($headers, function ($header) { return is_string($header) || $header instanceof RendererInterface; });
            $this->columnHeaders = array_slice($headers, 0, 2);
        }
        return $this;
    }

    /**
     * @param string|null $set
     * @param bool|null $keepExisting
     * @return string|null|static
     * @deprecated Keep for compatibility
     */
    public function fieldHeaderStyle(string $set = null, bool $keepExisting = null): string|null|static
    {
        Log::warning('DEPRECATED Renderable::fieldHeaderStyle() called.');
        return is_null($set) ? '' : $this;
    }

    /**
     * @param int|null $set
     * @return int|null|static
     * @see RenderableOptions::$tableLabelCellWidth
     * @deprecated Keep for compatibility
     */
    public function fieldHeaderWidth(int $set = null): int|null|static
    {
        Log::warning('DEPRECATED Renderable::fieldHeaderWidth() called.');
        // Make it compatible.
        if (is_null($set)) {
            $value = $this->tableLabelCellWidth();
            return (int) $value;
        }
        $this->tableLabelCellWidth("{$set}px");
        return $this;
    }

    /**
     * @param bool|null $set
     * @return bool|Renderable|static
     * @deprecated Keep for compatibility
     */
    public function renderTableHead(bool $set = null): bool|Renderable|static
    {
        Log::warning('DEPRECATED Renderable::renderTableHead() called.');
        if (is_null($set)) {
            return !empty($this->columnHeaders());
        }
        $this->columnHeaders($set, $set);
        return $this;
    }
}