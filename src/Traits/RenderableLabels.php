<?php namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;
use Wongyip\PHPHelpers\Format;

/**
 * @author wongyip
 */
trait RenderableLabels
{
    /**
     * Extract or generate labels automatically.
     *
     * @return static
     */
    public function autoLabels(): static
    {
        // Preset column name to labels.
        foreach (array_keys($this->attributes()) as $column) {
            $this->label($column, Format::smartCaps($column));
        }

        // Replace with labels from model if found,
        if (isset($this->model)) {
            try {
                if (method_exists($this->model, 'getLabels')) {
                    $labels = $this->model->getLabels();
                    if (is_array($labels)) {
                        $this->labels($labels);
                    }
                }
            }
            catch (Throwable $e) {
                Log::error('Renderable.labelFromModel() error: ' . $e->getMessage());
            }
        }

        return $this;
    }

    /**
     * Get or set the label of a column.
     *
     * Getter returns the label found in the local $labels array, or label
     * extracted from the input model (if it exists), in order. Fallback to
     * Format::smartCaps($column) if both of them are null.
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
                : ($this->labelFromModel($column) ?? Format::smartCaps($column));
        }
        // Set
        $this->labels = array_merge($this->labels, [$column => $label]);
        return $this;
    }

    /**
     * @note This will require a Model object, which implements this method:
     * @notr public function getLabel(string $column): ?string
     *
     * @param string $column
     * @return string|null
     */
    private function labelFromModel(string $column): ?string
    {
        if (isset($this->model)) {
            try {
                if (method_exists($this->model, 'getLabel')) {
                    $output = $this->model->getLabel($column);
                    if (is_string($output) && !empty($output)) {
                        return $output;
                    }
                }
            }
            catch (Throwable $e) {
                Log::error('Renderable.labelFromModel() error: ' . $e->getMessage());
            }
        }
        return null;
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