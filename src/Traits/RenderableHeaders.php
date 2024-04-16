<?php

namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\HTML\TagAbstract;

trait RenderableHeaders
{
    /**
     * The header tag of the "Field" column/row for run-time configurations,
     * where its tag-name and inner contents will be ignored on output.
     *
     * @var TagAbstract
     */
    public TagAbstract $fieldHeader;
    /**
     * The header tag of the "Value" column/row for run-time configurations,
     * where its tag-name and inner contents will be ignored on output.
     *
     * @var TagAbstract
     */
    public TagAbstract $valueHeader;

    /**
     * Get or set the "Field" header style. Setter default replace existing unless
     * $keepExisting is true.
     *
     * Although it could be done by $this->fieldHeader->styleAppend($setter), the
     * advantage of this method is that it returns the Renderable for chaining.
     *
     * @todo Grid layout support to be added.
     *
     * @param string|null $setter
     * @param bool|null $keepExisting
     * @return string|null|static
     */
    public function fieldHeaderStyle(string $setter = null, bool $keepExisting = null): string|null|static
    {
        // Get
        if (is_null($setter)) {
            return $this->fieldHeader->style();
        }
        // Set
        if (!$keepExisting) {
            // Replace
            $this->fieldHeader->styleEmpty();
        }
        $this->fieldHeader->styleAppend($setter);
        return $this;
    }

    /**
     * Get or set the "Field" header's width in pixels. Getter return null if
     * CSS width property is not set, or not in "px". Setter replace CSS width
     * property regardless of unit.
     *
     * @param int|null $setter
     * @return int|null|static
     */
    public function fieldHeaderWidth(int $setter = null): int|null|static
    {
        // Get
        if (is_null($setter)) {
            if ($value = $this->fieldHeader->styleProperty('width')) {
                if (preg_match("/(\d+)px/", $value, $match)) {
                    return (int) $match[1];
                }
            }
            return null;
        }
        // Set
        $this->fieldHeader->styleProperty('width', $setter . 'px', true);
        return $this;
    }
}