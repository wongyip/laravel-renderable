<?php

namespace Wongyip\Laravel\Renderable\Components;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Wongyip\HTML\Anchor;
use Wongyip\HTML\RawHTML;
use Wongyip\HTML\Tag;
use Wongyip\HTML\TagAbstract;
use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\Laravel\Renderable\Tags\Icon;
use Wongyip\Laravel\Renderable\Tags\LinkWithIcon;
use Wongyip\Laravel\Renderable\Tags\Raw;
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
     * Convert the given $value to string/int/float for output.
     *
     * @param mixed|null $value
     * @return int|float|string
     */
    private function flatten(mixed $value = null): int|float|string
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

    /**
     * Static constructor.
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
     * Export a label tag base on options.
     *
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
     * Export a value tag base on options.
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
        $method = 'valueTag' . ucfirst(strtolower($this->options->type));
        if (method_exists($this, $method)) {
            $tag = $this->$method($tagName);
        }
        else {
            // Oops
            Log::warning(
                sprintf(
                    'Column.valueTag: column %s with data type %s is not supported (method %s not found).',
                    $this->options->type,
                    $this->name,
                    $method
                )
            );
            $tag = Tag::make($tagName)->contents($this->flatten($this->value));
        }
        return $tag->class(Renderable::CSS_CLASS_VALUE);
    }

    /**
     *  Make a value tag in 'bool' type.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagBool(string $tagName): TagAbstract
    {
        $value = is_bool($this->value)
            ? ($this->value ? $this->options->valueTrue : $this->options->valueFalse)
            : $this->options->valueNull;
        return Tag::make($tagName)->contents($value);
    }

    /**
     *  Make a value tag in 'csv' type.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagCsv(string $tagName): TagAbstract
    {
        $csv = is_array($this->value) ? implode($this->options->glue, $this->value) : $this->value;
        return Tag::make($tagName)->contents($this->flatten($csv));
    }

    /**
     *  Make a value tag in 'html' type.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagHtml(string $tagName): TagAbstract
    {
        return Tag::make($tagName)
            ->contents(
                RawHTML::create($this->flatten($this->value))
            );
    }

    /**
     *  Make a value tag in 'lines' type.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagLines(string $tagName): TagAbstract
    {
        $html = '';
        foreach ((is_array($this->value) ? $this->value : [$this->value]) as $value) {
            $html .= htmlspecialchars($this->flatten($value)) . '<br/>';
        }
        return Tag::make($tagName)->contents(RawHTML::create($html));
    }

    /**
     * Make a value tag in 'link' type.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagLink(string $tagName): TagAbstract
    {
        $link = Anchor::create($this->value, $this->value);
        if ($this->options->icon) {
            if ($this->options->iconPosition === ColumnOptions::ICON_POSITION_BEFORE) {
                $link->contentsPrepend(Icon::create($this->options->icon, true), RawHTML::NBSP());
            }
            else {
                $link->contentsAppend(RawHTML::NBSP(), Icon::create($this->options->icon, true));
            }
        }
        return Tag::make($tagName)->contents($link);
    }

    /**
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagList(string $tagName): TagAbstract
    {
        $items = is_array($this->value) ? $this->value : [$this->value];
        $ul = Tag::make($this->options->type)
            ->class($this->options->listClass)
            ->style($this->options->listStyle);
        foreach ($items as $item) {
            $ul->contentsAppend(
                Tag::make('li')
                    ->contents($this->flatten($item))
                    ->class($this->options->itemClass)
                    ->style($this->options->itemStyle)
            );
        }
        return Tag::make($tagName)->contents($ul);
    }

    /**
     * Make a value tag in 'ol' type.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagOl(string $tagName): TagAbstract
    {
        return $this->valueTagList($tagName);
    }
    /**
     * Make a value tag in 'string' type.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagString(string $tagName): TagAbstract
    {
        Log::debug(sprintf('Column.valueTagString() - column %s is typed as string instead of text.', $this->name));
        $escaped = htmlspecialchars($this->flatten($this->value));
        return Tag::make($tagName)->contents($escaped);
    }

    /**
     * Make a value tag in 'text' type.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagText(string $tagName): TagAbstract
    {
        $content = RawHTML::create(nl2br(htmlspecialchars($this->flatten($this->value))));
        return Tag::make($tagName)->contents($content);
    }

    /**
     * Make a value tag in 'ul' type.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    private function valueTagUl(string $tagName): TagAbstract
    {
        return $this->valueTagList($tagName);
    }
}