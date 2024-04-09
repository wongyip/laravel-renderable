<?php

namespace Wongyip\Laravel\Renderable\Components;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Wongyip\HTML\RawHTML;
use Wongyip\HTML\Tag;
use Wongyip\HTML\TagAbstract;
use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\PHPHelpers\Format;

class Column
{
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
     * Compose a Column object, usually for
     *
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
        return Tag::make($tagName)->class(Renderable::CSS_CLASS_LABEL)->contents(
            $this->labelHTML
                ? RawHTML::create($this->labelHTML)
                : $this->label
        );
    }

    /**
     * Export a value tag for render.
     *
     * N.B. returned tag may contain unsafe attributes, especially when input
     * contains user-contributed contents, make sure you sanitize it before
     * actually output the HTML.
     *
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
                $content = RawHTML::create(nl2br(htmlspecialchars($this->valueFlattened($this->value))));
                $tag = Tag::make($tagName)->contents($content);
                break;
            case 'bool':
                $value = is_bool($this->value)
                    ? ($this->value ? $this->options->valueTrue : $this->options->valueFalse)
                    : $this->options->valueNull;
                $tag = Tag::make($tagName)->contents($value);
                break;
            case 'csv':
                $csv = is_array($this->value) ? implode($this->options->glue, $this->value) : $this->value;
                $tag = Tag::make($tagName)->contents($this->valueFlattened($csv));
                break;
            case 'ol':
            case 'ul':
                $items = is_array($this->value) ? $this->value : [$this->value];
                $ul = Tag::make($this->options->type)
                    ->class($this->options->listClass)
                    ->style($this->options->listStyle);
                foreach ($items as $item) {
                    $ul->contentsAppend(
                        Tag::make('li')
                            ->contents($this->valueFlattened($item))
                            ->class($this->options->itemClass)
                            ->style($this->options->itemStyle)
                    );
                }
                $tag = Tag::make($tagName)->contents($ul);
                break;
            case 'html':
                $tag = Tag::make($tagName)->contents(RawHTML::create($this->valueFlattened($this->value)));
                break;
            case 'lines':
                $html = '';
                foreach ((is_array($this->value) ? $this->value : [$this->value]) as $value) {
                    $html .= htmlspecialchars($this->valueFlattened($value)) . '<br/>';
                }
                $tag = Tag::make($tagName)->contents(RawHTML::create($html));
                break;
            default:
                // Oops
                Log::warning(
                    sprintf('Column.valueTag: unrecognized data type %s or column %s.',$this->options->type, $this->name)
                );
                $tag = Tag::make($tagName)->contents($this->valueFlattened($this->value));
        }
        return $tag->class(Renderable::CSS_CLASS_VALUE);
    }

    /**
     * Convert the given $value to string/int/float for output.
     *
     * @param mixed|null $value
     * @return int|float|string
     */
    private function valueFlattened(mixed $value = null): int|float|string
    {
        try {
            if (is_scalar($value)) {
                // Boolean to 'Yes' or 'No' or other strings configured.
                if (is_bool($value)) {
                    return $value ? $this->options->valueTrue : $this->options->valueFalse;
                }
                // Unchanged for int, float and string.
                return $value;
            }
            elseif (is_null($value)) {
                return $this->options->valueNull;
            }
            elseif (is_array($value)) {
                // Array to CSV string
                return implode($this->options->glue, array_values($value));
            }
            // DateTime to string
            elseif ($value instanceof DateTime) {
                return $value->format(LARAVEL_RENDERABLE_DATETIME_FORMAT);
            }
            elseif (is_object($value)) {
                Log::error(sprintf('Column.valueFlattened() return empty string: type (%s) has no handler.', get_class($value)));
            }
            elseif (is_resource($value)) {
                Log::notice('Column.valueFlattened() return empty string as value is a resource.');
            }
            else {
                Log::error('Column.valueFlattened() return empty string: unexpected type.');
            }
        }
        catch (Exception $e) {
            Log::error(sprintf('Column.valueFlattened() conversion failure: exception [%d] %s', $e->getCode(), $e->getMessage()));
        }
        return '';
    }
}