<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\Renderable;

/**
 * All simple, strict forward options, properties, etc.
 * 
 * @author wongyip
 */
trait PublicPropTrait
{
    /**
     * @var string
     */
    public string $captionField = 'Field';
    /**
     * @var string
     */
    public string $captionValue = 'Value';
    /**
     * @var string
     */
    public string $containerClass = '';
    /**
     * @var string
     */
    public string $containerId = '';
    /**
     * @var string
     */
    public string $containerStyl = '';
    /**
     * @var string
     */
    public string $fieldClass = '';
    /**
     * @var string
     */
    public string $fieldHeaderClass = '';
    /**
     * @var string
     */
    public string $fieldHeaderStyle = '';
    /**
     * @var string
     */
    public string $fieldStyle = '';
    /**
     * @var string
     */
    public string $tableCaption = '';
    /**
     * @var string
     */
    public string $tableClass = 'table table-bordered table-hover';
    /**
     * @var string
     */
    public string $tableStyle = '';
    /**
     * @var string
     */
    public string $tableHeadClass = 'thead-light';
    /**
     * @var string
     */
    public string $tableResponsive = '';
    /**
     * @var string
     */
    public string $tableHeadStyle = '';
    /**
     * @var string
     */
    public string $tableLabelCellTag = 'th';
    /**
     * @var string
     */
    public string $labelClass = '';
    /**
     * @var string
     */
    public string $labelStyle = '';
    /**
     * @var string
     */
    public string $valueClass = '';
    /**
     * @var string
     */
    public string $valueHeaderClass = '';
    /**
     * @var string
     */
    public string $valueHeaderStyle = '';
    /**
     * @var string
     */
    public string $valueStyle = '';

    /**
     * Get or set the field header style.
     *
     * @param string|null $setter
     * @param bool|null $append
     * @return string|null|Renderable
     */
    public function fieldHeaderStyle(string $setter = null, bool $append = null): string|null|Renderable
    {
        if ($setter) {
            $this->fieldHeaderStyle = $append
                ? ltrim(str_replace(';;', ';', implode(';', [$this->fieldHeaderStyle, $setter])), ';')
                : $setter;
            return $this;
        }
        return $this->fieldHeaderStyle;
    }

    /**
     * Get or set the field header's width in pixels.
     *
     * @param int|null $setter
     * @return int|null|Renderable
     */
    public function fieldHeaderWidth(int $setter = null): int|null|Renderable
    {
        if ($setter) {
            $rule = sprintf('width: %dpx', $setter);
            $this->fieldHeaderStyle = ltrim(str_replace(';;', ';', implode(';', [$this->fieldHeaderStyle, $rule])), ';');
            return $this;
        }
        return preg_match("/width:(\s+)?(\d+)px/", $this->fieldHeaderStyle, $matches)
            ? (int) $matches[2]
            : null;
    }
}