<?php

namespace Wongyip\Laravel\Renderable\Components;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Wongyip\HTML\Tag;
use Wongyip\HTML\TagAbstract;
use Wongyip\Laravel\Renderable\Traits\CssClass;
use Wongyip\PHPHelpers\Format;

class Column
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
     * @param mixed $value
     * @param string|null $label
     * @param string|null $labelHTML
     * @param ColumnOptions|null $options
     */
    public function __construct(string $name, mixed $value, string $label = null, string $labelHTML = null,  ColumnOptions $options = null)
    {
        $this->name      = $name;
        $this->value     = $value;
        $this->label     = $label ?? Format::smartCaps($name);
        $this->labelHTML = $labelHTML ?? '';
        $this->options   = $options ?? ColumnOptions::defaults();
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param string|null $label
     * @param string|null $labelHTML
     * @param ColumnOptions|null $options
     * @return static
     */
    public static function init(string $name, mixed $value, string $label = null, string $labelHTML = null, ColumnOptions $options = null): static
    {
        return new static($name, $value, $label, $labelHTML, $options);
    }

    /**
     * @param string $tagName
     * @return TagAbstract
     */
    public function labelTag(string $tagName): TagAbstract
    {
        return Tag::make($tagName)->contents(
            $this->labelHTML
                ? RawHTML::create($this->labelHTML)
                : $this->label
        );
    }

    /**
     * @param string $tagName
     * @return TagAbstract
     */
    public function valueTag(string $tagName): TagAbstract
    {
        switch ($this->options->type) {
            /**
             * Plain text are
             */
            case 'text';
            case 'string':
                $content = RawHTML::create(nl2br(htmlspecialchars($this->safeValue($this->value))));
                return Tag::make($tagName)->contents($content);
            case 'bool':
                $value = is_bool($this->value)
                    ? ($this->value ? $this->options->valueTrue : $this->options->valueFalse)
                    : $this->options->valueNull;
                return Tag::make($tagName)->contents($value);
            case 'csv':
                $csv = is_array($this->value) ? implode($this->options->glue, $this->value) : $this->value;
                return Tag::make($tagName)->contents($this->safeValue($csv));
            case 'ol':
            case 'ul':
                $items = is_array($this->value) ? $this->value : [$this->value];
                $ul = Tag::make($this->options->type)
                    ->class($this->options->listClass)
                    ->style($this->options->listStyle);
                foreach ($items as $item) {
                    $ul->contentsAppend(
                        Tag::make('li')
                            ->contents($this->safeValue($item))
                            ->class($this->options->itemClass)
                            ->style($this->options->itemStyle)
                    );
                }
                return Tag::make($tagName)->contents($ul);
            case 'lines':
                $html = '';
                foreach ((is_array($this->value) ? $this->value : [$this->value]) as $value) {
                    $this->prepareValue($value);
                    $html .= htmlspecialchars($value) . '<br/>';
                }
                return Tag::make($tagName)->contents(RawHTML::create($html));
            default:
                Log::warning(
                    sprintf('Column.valueTag: unrecognized data type %s or column %s.',
                        $this->options->type,
                        $this->name
                    )
                );
                return Tag::make($tagName)->contents($this->safeValue($this->value));

        }
    }

    private function prepareValues(array &$values): void
    {
        foreach ($values as $key => $value) {
            $values[$key] = $this->safeValue($value);
        }
    }

    private function prepareValue(mixed &$value): void
    {
        $values = $this->safeValue($value);
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
                Log::info('Column.valueRenderable() Typed csv but value is not array, returning safe value.');
                return $this->safeValue($this->value);
        }
        Log::debug('Column.valueRenderable() Untyped column, returning safe value.');
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
                Log::error(sprintf('Column.safeValue() return empty string: type (%s) has no handler.', get_class($input)));
            }
            elseif (is_resource($input)) {
                Log::notice('Column.safeValue() return empty string as value is a resource.');
            }
            else {
                Log::error('Column.safeValue() return empty string: unexpected type.');
            }
        }
        catch (Exception $e) {
            Log::error(sprintf('Column.safeValue() conversion failure: exception [%d] %s', $e->getCode(), $e->getMessage()));
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

    public function addAttrs(): array
    {
        return [];
    }
}