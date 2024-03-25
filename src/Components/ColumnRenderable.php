<?php namespace Wongyip\Laravel\Renderable\Components;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Wongyip\Laravel\Renderable\Traits\CssClass;

/**
 * Compiled output for the views.
 *
 * Noted that this class is not intended to have any change after instantiation,
 * it should be kept for presentation use only.
 *
 * @todo Add HtmlAttributes trait.
 */
class ColumnRenderable
{
    use CssClass;
    /**
     * @var string
     */
    public string $label;
    /**
     * @var string
     */
    public string $labelHTML;
    /**
     * @var string
     */
    public string $name;
    /**
     * @var ColumnOptions
     */
    public ColumnOptions $options;
    /**
     * @var string
     */
    public string $type;
    /**
     * @var mixed
     */
    public mixed $value;

    /**
     * @param string $name
     * @param mixed|null $value
     * @param string|null $type
     * @param string|null $label
     * @param string|null $labelHTML
     * @param array|ColumnOptions|null $options
     */
    public function __construct(string $name, mixed $value = null, string $type = null, string $label = null, string $labelHTML = null, array|ColumnOptions $options = null)
    {
        $options = $options
            ? (is_array($options) ? new ColumnOptions($options) : $options)
            : ColumnOptions::defaults();
        $this->name      = $name;
        $this->value     = $value;
        $this->type      = $type ?? config('renderable.default.type');
        $this->label     = $label ?? '';
        $this->labelHTML = $labelHTML ?? '';
        $this->options   = $options;
    }

    /**
     * @inheritdoc
     * @see CssClass::classesHook()
     */
    protected function classesHook(array $classes): array
    {
        array_push(
            $classes,
            'renderable-column',
            'renderable-column-' . $this->name
        );
        return $classes;
    }

    /**
     * Get the formatted / parsed value for rendering based on type and options.
     *
     * @return mixed
     */
    public function valueRenderable(): mixed
    {
        // Localize
        $type = $this->type;
        $value = $this->value;
        switch ($type) {
            // These types must be an array, so the view could handle it correctly.
            case 'ol':
            case 'ul':
            case 'lines':
                if (!is_array($this->value)) {
                    Log::warning(sprintf('Type %s column with non-array value, patched.', $this->type));
                    return [$this->value];
                }
                return $this->value;
            case 'boolean': // @todo To be removed.
            case 'bool':
                return is_null($this->value)
                    ? ($this->options->valueNull ?? $this->options->valueFalse)
                    : ($this->value ? $this->options->valueTrue : $this->options->valueFalse);
            case 'csv':
                if (is_array($this->value)) {
                    return implode($this->options->glue, array_values($this->value));
                }
                Log::info('ColumnRenderable.valueRenderable() Typed csv but value is not array, returning safe value.');
                return $this->safeValue($this->value);
        }
        Log::debug('ColumnRenderable.valueRenderable() Untyped column, returning safe value.');
        return $this->safeValue($value);
    }

    /**
     * Convert the given $value to string for output.
     *
     * @param mixed|null $input
     * @return int|float|string
     */
    private function safeValue(mixed $input = null): int|float|string
    {
        try {
            if (is_scalar($input)) {
                // Boolean to 'Yes' or 'No' or other strings configured.
                if (is_bool($input)) {
                    return $input ? $this->options->valueTrue : $this->options->valueFalse;
                }
                // Unchanged for int, float and string.
                return $input;
            }
            elseif (is_null($input)) {
                return $this->options->valueNull;
            }
            elseif (is_array($input)) {
                // Array to default format.
                return  implode($this->options->glue, array_values($input));
            }
            // DateTime to string
            elseif ($input instanceof DateTime) {
                return $input->format(LARAVEL_RENDERABLE_DATETIME_FORMAT);
            }
            elseif (is_object($input)) {
                Log::error(sprintf('ColumnRenderable.safeValue() return empty string: type (%s) has no handler.', get_class($input)));
            }
            elseif (is_resource($input)) {
                Log::notice('ColumnRenderable.safeValue() return empty string as value is a resource.');
            }
            else {
                Log::error('ColumnRenderable.safeValue() return empty string: unexpected type.');
            }
        }
        catch (Exception $e) {
            Log::error(sprintf('ColumnRenderable.safeValue() conversion failure: exception [%d] %s', $e->getCode(), $e->getMessage()));
        }
        return '';
    }

    /**
     * @return bool
     */
    public function isRenderable(): bool
    {
        return $this->valueRenderable() !== false;
    }
}