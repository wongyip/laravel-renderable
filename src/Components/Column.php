<?php

namespace Wongyip\Laravel\Renderable\Components;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Wongyip\HTML\Anchor;
use Wongyip\HTML\Div;
use Wongyip\HTML\RawHTML;
use Wongyip\HTML\Interfaces\RendererInterface;
use Wongyip\HTML\Tag;
use Wongyip\HTML\TagAbstract;
use Wongyip\Laravel\Renderable\Renderable;
use Wongyip\Laravel\Renderable\Tags\Icon;
use Wongyip\Laravel\Renderable\Utils\HTML;
use Wongyip\PHPHelpers\Format;

class Column
{
    /**
     * @var string
     */
    public string|RendererInterface $label;
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
     * @param string|RendererInterface|null $label
     * @param ColumnOptions|null $options
     */
    public function __construct(string $name, mixed $value, string|RendererInterface $label = null, ColumnOptions $options = null)
    {
        $this->name      = $name;
        $this->value     = $value;
        $this->label     = $label ?? Format::smartCaps($name);
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
     * @param string|RendererInterface|null $label
     * @param ColumnOptions|null $options
     * @return static
     */
    public static function init(string $name, mixed $value, string|RendererInterface $label = null, ColumnOptions $options = null): static
    {
        return new static($name, $value, $label, $options);
    }

    /**
     * Export a label tag base on options.
     *
     * @param string $tagName
     * @return TagAbstract
     */
    public function labelTag(string $tagName): TagAbstract
    {
        return Tag::make($tagName)
            ->class(Renderable::CSS_CLASS_LABEL)
            ->contents($this->label);
    }

    /**
     * Make a value tag in 'bool' type.
     *
     * @return RendererInterface|string
     */
    private function valueContentBool(): RendererInterface|string
    {
        return is_bool($this->value)
            ? ($this->value ? $this->options->valueTrue : $this->options->valueFalse)
            : $this->options->valueNull;
    }

    /**
     * Make a value tag in 'csv' type.
     *
     * @return RendererInterface|string
     */
    private function valueContentCsv(): RendererInterface|string
    {
        $csv = is_array($this->value) ? implode($this->options->glue, $this->value) : $this->value;
        return $this->flatten($csv);
    }

    /**
     * When type if unrecognized.
     *
     * @return RendererInterface|string
     */
    private function valueContentDefault(): RendererInterface|string
    {
        Log::debug(sprintf('Column.valueContentDefault() - column %s is rendered as default flatten text.', $this->name));
        return $this->flatten($this->value);
    }

    /**
     * Make a value tag in 'html' type. User contributed contents are strictly
     * sanitized before output.
     *
     * @return RendererInterface|string
     */
    private function valueContentHtml(): RendererInterface|string
    {
        /**
         * ALWAYS STRICT SANITIZED USER CONTRIBUTED CONTENTS.
         */
        $sanitizedHtml = HTML::purify($this->flatten($this->value));
        return RawHTML::create($sanitizedHtml);
    }

    /**
     * Make a value tag in 'lines' type.
     *
     * @return RendererInterface|string
     */
    private function valueContentLines(): RendererInterface|string
    {
        $html = '';
        foreach ((is_array($this->value) ? $this->value : [$this->value]) as $value) {
            $html .= htmlspecialchars($this->flatten($value)) . '<br/>';
        }
        return RawHTML::create($html);
    }

    /**
     * Make a value tag in 'link' type.
     *
     * @return RendererInterface|string
     */
    private function valueContentLink(): RendererInterface|string
    {
        $link = Anchor::create($this->value, $this->options->linkText ?? $this->value);
        if ($this->options->icon) {
            if ($this->options->iconPosition === ColumnOptions::ICON_POSITION_BEFORE) {
                $link->contentsPrepend(Icon::create($this->options->icon, true), RawHTML::NBSP());
            }
            else {
                $link->contentsAppend(RawHTML::NBSP(), Icon::create($this->options->icon, true));
            }
        }
        return $link;
    }

    /**
     * Mother of OL and UL.
     *
     * @return RendererInterface|string
     */
    private function valueContentList(): RendererInterface|string
    {
        $items = is_array($this->value) ? $this->value : [$this->value];
        $list = Tag::make($this->options->type)
            ->class($this->options->listClass)
            ->style($this->options->listStyle);
        foreach ($items as $item) {
            $list->contentsAppend(
                Tag::make('li')
                    ->contents($this->flatten($item))
                    ->class($this->options->itemClass)
                    ->style($this->options->itemStyle)
            );
        }
        return $list;
    }

    /**
     * Make a value tag in 'ol' type.
     *
     * @return RendererInterface|string
     */
    private function valueContentOl(): RendererInterface|string
    {
        return $this->valueContentList();
    }
    /**
     * Make a value tag in 'string' type.
     *
     * @return RendererInterface|string
     */
    private function valueContentString(): RendererInterface|string
    {
        Log::debug(sprintf('Column.valueTagString() - column %s is typed as string instead of text.', $this->name));
        return htmlspecialchars($this->flatten($this->value));
    }

    /**
     * Make a value tag in 'text' type.
     *
     * @return RendererInterface|string
     */
    private function valueContentText(): RendererInterface|string
    {
        return RawHTML::create(nl2br(htmlspecialchars($this->flatten($this->value))));
    }

    /**
     * Make a value tag in 'ul' type.
     *
     * @return RendererInterface|string
     */
    private function valueContentUl(): RendererInterface|string
    {
        return $this->valueContentList();
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
     * @use static::valueContentBool()
     * @use static::valueContentCsv()
     * @use static::valueContentHtml()
     * @use static::valueContentLine()
     * @use static::valueContentLink()
     * @use static::valueContentList()
     * @use static::valueContentOl()
     * @use static::valueContentString()
     * @use static::valueContentText()
     * @use static::valueContentUl()
     */
    public function valueTag(string $tagName): TagAbstract
    {
        /**
         * Format the content by its type.
         *
         * @var RendererInterface|string $content
         */
        $method = 'valueContent' . ucfirst(strtolower($this->options->type));
        $content = method_exists($this, $method)
            ? $this->$method()
            : $this->valueContentDefault();


        // Value wrapper.
        $container = Div::tag($content)->classAppend(Renderable::CSS_CLASS_VALUE_CONTAINER);

        /**
         * Scrolling long content: wrap content in container (DIV tag).
         */
        if (isset($this->options->scrolling) && $this->options->scrolling) {
            /**
             * @var TagAbstract $scrollingContent
             */
            $container->styleAppend(
                match ($this->options->scrolling) {
                    Renderable::CONTENT_SCROLLING_AUTO => 'overflow: auto',
                    Renderable::CONTENT_SCROLLING_X => 'overflow-x: auto',
                    Renderable::CONTENT_SCROLLING_Y => 'overflow-y: auto',
                    default => '',
                },
                isset($this->options->maxHeight) ? sprintf('max-height: %dpx', $this->options->maxHeight): '',
                isset($this->options->maxWidth) ? sprintf('max-width: %dpx', $this->options->maxWidth): '',
            );

        }

        return Tag::make($tagName)
            ->contents($container)
            ->classAppend(Renderable::CSS_CLASS_VALUE);
    }
}