<?php namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;
use Wongyip\HTML\Interfaces\RendererInterface;
use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\PHPHelpers\Format;

/**
 * @author wongyip
 */
trait ColumnLabels
{
    use GetSetters;

    /**
     * Column labels in plain-text.
     *
     * @var array|string[]|RendererInterface[]
     */
    protected array $labels = [];

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
                Log::error(sprintf('Renderable.autoLabels(): Unable to set model label (%s).', $e->getMessage()));
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
     * @param string|RendererInterface|null $label
     * @return string|RendererInterface|Renderable|static
     */
    public function label(string $column, string|RendererInterface $label = null): string|RendererInterface|Renderable|static
    {
        // Get
        if (is_null($label)) {
            return key_exists($column, $this->labels)
                ? $this->labels[$column]
                : ($this->labelFromModel($column) ?? Format::smartCaps($column));
        }
        // Set
        $this->labels[$column] = $label;
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
     * Get or set labels of columns, setter merge into existing $labels.
     *
     * @param array|null $labels
     * @return array|string[]|RendererInterface[]|Renderable|static
     */
    public function labels(array $labels = null): array|Renderable|static
    {
        // Get
        if (is_null($labels)) {
            return $this->labels ?? [];
        }
        // Set
        $this->labels = array_merge($this->lables ?? [], $labels);
        return $this;
    }
}