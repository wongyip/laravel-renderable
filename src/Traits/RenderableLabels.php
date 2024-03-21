<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\Laravel\Renderable\ModelRenderable;
use Wongyip\PHPHelpers\Format;

/**
 * @author wongyip
 */
trait RenderableLabels
{
    use RenderableGetSetters;

    /**
     * Column labels in plain-text.
     *
     * @var array|string[]
     */
    protected array $labels = [];
    /**
     * Columns labels that should be rendered as HTML (Twig: |raw filter).
     *
     * @var array|string[]
     */
    protected array $labelsHTML = [];
    
    /**
     * @return ModelRenderable
     */
    public function autoLabels()
    {
        $labels = [];
        foreach (array_keys($this->attributes()) as $column) {
            $labels[$column] = Format::smartCaps($column);
        }
        return $this->labels($labels);
    }

    /**
     * Get or set the label of a column, getter returns smartCaps($column) if no
     * label is set.
     *
     * @param string $column
     * @param string|null $label
     * @return string|static
     */
    public function label(string $column, string $label = null): string|static
    {
        // Get
        if (is_null($label)) {
            return key_exists($column, $this->labels)
                ? $this->labels[$column]
                : Format::smartCaps($column);
        }
        // Set
        $this->labels = array_merge($this->labels, [$column => $label]);
        return $this;
    }

    /**
     * Get or set the label of a column in HTML format, getter returns null if no
     * HTML label is set.
     *
     * @param string $column
     * @param string|null $labelHTML
     * @return string|null|static
     */
    public function labelHTML(string $column, string $labelHTML = null): string|null|static
    {
        // Get
        if (is_null($labelHTML)) {
            return key_exists($column, $this->labelsHTML)
                ? $this->labelsHTML[$column]
                : null;
        }
        // Set
        $this->labelsHTML = array_merge($this->labelsHTML, [$column => $labelHTML]);
        return $this;
    }

    /**
     * Get or set labels of columns, setter merge into existing $labels unless
     * $replace is TRUE.
     *
     * @param array|null $labels
     * @param bool $replace
     * @return array|string[]|static
     */
    public function labels(array $labels = null, bool $replace = false): array|static
    {
        return $this->__getSetMergeArray('labels', $labels, $replace);
    }

    /**
     * Get or set labels of columns in HTML format, setter merge into existing
     * $labelsHHTML $replace is TRUE.
     *
     * @param array|null $labelsHTML
     * @param bool $replace
     * @return array|string[]|static
     */
    public function labelsHTML(array $labelsHTML = null, bool $replace = false): array|static
    {
        return $this->__getSetMergeArray('labelsHTML', $labelsHTML, $replace);
    }
}