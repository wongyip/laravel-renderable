<?php namespace Wongyip\Laravel\Renderable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Wongyip\HTML\Beautify;
use Wongyip\HTML\Interfaces\RendererInterface;
use Wongyip\HTML\Tag;
use Wongyip\Laravel\Renderable\Components\RenderableOptions;
use Wongyip\Laravel\Renderable\Traits\Bootstrap4Trait;
use Wongyip\Laravel\Renderable\Traits\ColumnContents;
use Wongyip\Laravel\Renderable\Traits\LayoutGrid;
use Wongyip\Laravel\Renderable\Traits\Attributes;
use Wongyip\Laravel\Renderable\Traits\Columns;
use Wongyip\Laravel\Renderable\Traits\ColumnHeaders;
use Wongyip\Laravel\Renderable\Traits\ColumnLabels;
use Wongyip\Laravel\Renderable\Traits\LayoutTable;
use Wongyip\Laravel\Renderable\Traits\ColumnTypes;
use Wongyip\Laravel\Renderable\Utils\HTML;

/**
 * @see RenderableOptions
 * @see /config/renderable.php
 * -----------------------------------------------------------------------------
 * @method string|Renderable id(string $id = null)
 * @method RenderableOptions|Renderable options(RendererInterface $options = null)
 * -----------------------------------------------------------------------------
 * @method bool|Renderable beautifyHTML(bool $set = null)
 * @method string|Renderable containerIdSuffix(string $set = null)
 * @method string|Renderable emptyRecord(string $set = null)
 * @method string|Renderable gridClassAppend(string $set = null)
 * @method string|Renderable gridClassPrepend(string $set = null)
 * @method string|Renderable idPrefix(string $set = null)
 * @method string|RendererInterface|Renderable prefix(string|RendererInterface $set = null)
 * @method string|RendererInterface|Renderable suffix(string|RendererInterface $set = null)
 * @method bool|Renderable tableBordered(bool $set = null) Get or toggle table-bordered styling.
 * @method bool|Renderable tableBorderless(bool $set = null) Get or toggle table-borderless styling.
 * @method string|Renderable tableCaptionSide(string $set = null)
 * @method string|Renderable tableClassAppend(string $set = null)
 * @method string|Renderable tableClassBase(string $set = null)
 * @method string|Renderable tableClassPrepend(string $set = null)
 * @method bool|Renderable tableHorizontalHeaders(bool $set = null)
 * @method bool|Renderable tableHover(bool $set = null) Get or toggle table-hover styling.
 * @method string|Renderable tableLabelCellWidth(string $set = null) Get or set CSS width of 'tbody > tr > td:first-child', integer value will be suffixed with default unit 'px' on output.
 * @method bool|Renderable tableStriped(bool $set = null) Get or toggle table-striped styling.
 */
class Renderable implements RendererInterface
{
    use Attributes, Columns, ColumnContents, ColumnHeaders, ColumnLabels, ColumnTypes;

    // Layouts
    use LayoutGrid, LayoutTable;

    // @todo Review needed.
    use Bootstrap4Trait;

    const CSS_CLASS_BODY            = 'renderable-body';
    const CSS_CLASS_CONTAINER       = 'renderable-container';
    const CSS_CLASS_GRID            = 'renderable-grid';
    const CSS_CLASS_LABEL           = 'renderable-label';
    const CSS_CLASS_TABLE           = 'renderable-table';
    const CSS_CLASS_TABLE_HEAD      = 'thead-light';
    const CSS_CLASS_VALUE           = 'renderable-value';
    const CSS_CLASS_VALUE_CONTAINER = 'renderable-value-container';
    const LAYOUT_DEFAULT            = 'table';
    const LAYOUT_TABLE              = 'table';
    const LAYOUT_GRID               = 'grid';

    private array $__exposed = [
        'id', 'options',
    ];

    /**
     * The ID attribute of the main renderable HTML tag. Noted that this ID will
     * be prefixed with $this->options->idPrefix on render().
     *
     * @var string
     * @see Renderable::idPrefixed()
     */
    protected string $id;
    /**
     * Layout to be rendered. Note that this is an empty string by default, so
     * that the layout() method could detect layout change and run set up the
     * object accordingly.
     *
     * @var string
     * @see LayoutGrid
     * @see LayoutTable
     * @see Renderable::layout()
     */
    protected string $layout = '';
    /**
     * The container of the main tag. Note that runtime value of the ID attribute
     * is ignored by the render() method.
     *
     * @var RendererInterface
     */
    protected RendererInterface $container;
    /**
     * Options and switches.
     *
     * @var RenderableOptions
     * @see /config/renderable.php
     */
    protected RenderableOptions $options;

    /**
     * The Renderable object.
     *
     * IMPORTANT NOTE: The default behavior is render nothing unless a valid
     * non-null value is provided in the $included argument, this is important
     * to prevent data-leakage.
     *
     * 1. Note for the $included argument:
     *     - NULL: Use default (empty array), so no columns will be rendered.
     *     - TRUE: include all columns.
     *     - String: include a single column explicitly.
     *     - Array: include multiple columns explicitly.
     *
     * 2. Note for the $options argument:
     *     - NULL: take all defaults from config('renderable.options').
     *     - RenderableOptions object: ignore defaults.
     *     - Array: merge into default options.
     *
     * 3. Note for the $attributes argument:
     *     - Array: simply set the $attributes array.
     *     - Eloquent model: takes output of toArray() as $attributes, where
     *       hidden attributes and not appended accessors/mutators are not
     *       taken. However, they WILL BE RENDERED if they're "$included"
     *       and not "$excluded".
     *
     * @param array|Model $attributes Source attributes array or Eloquent Model.
     * @param array|string[]|string|null $included Default null for none, set TRUE to include all columns, string or array to include column(s) explicitly.
     * @param array|string[]|string|null $excluded Default null for none.
     * @param array|RenderableOptions|null $options Customization and switches options.
     * @param string|null $layout skip for LAYOUT_DEFAULT.
     */
    public function __construct(array|Model $attributes, bool|array|string $included = null, array|string $excluded = null, array|RenderableOptions $options = null, string $layout = null)
    {
        // Init.
        $this->id = crc32(uniqid());

        // Take it, or make a default with $options argument (null for all defaults, array to override matching defaults).
        $this->options = is_a($options, RenderableOptions::class) ? $options : new RenderableOptions($options);

        // Take model or attributes.
        if ($attributes instanceof Model) {
            $this->model = $attributes;
            $this->attributes = $this->model->toArray();
        }
        else {
            $this->attributes = $attributes;
        }

        // Wrapper (no need to set ID), and other components.
        $this->container = Tag::make('div')->classAdd(static::CSS_CLASS_CONTAINER);

        // Take other params.
        $this->layout($layout ?? static::LAYOUT_DEFAULT);
        $this->include($included);
        $this->exclude($excluded);

        // Automation
        $this->autoLabels();
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Renderable
     * @throws Exception
     */
    public function __call(string $name, array $arguments)
    {
        // Properties get-setters
        if (in_array($name, $this->__exposed)) {
            $set = $arguments[0] ?? null;
            if (is_null($set)) {
                return $this->$name ?? null;
            }
            $this->$name = $set;
            return $this;
        }

        // Options get-setters
        if (property_exists($this->options, $name)) {
            $set = $arguments[0] ?? null;
            // Get
            if (is_null($set)) {
                return $this->options->$name ?? null;
            }
            // Set
            $this->options->$name = $set;

            /**
             * Disable the opposite option on enabled. Note that they could be
             * disabled at the same time, so it's not necessary to enable the
             * opposite one on disabled.
             */
            if ($set) {
                if ($name === 'tableBorderless') {
                    $this->options->tableBordered = false;
                }
                elseif ($name === 'tableBordered') {
                    $this->options->tableBorderless = false;
                }
            }

            return $this;
        }

        throw new Exception(sprintf('Undefined method "%s.%s()" called.', (new ReflectionClass($this))->getShortName(), $name));
    }

    /**
     * The ID of the main tag rendered, take this if a selector is needed for
     * scripting or styling.
     *
     * @return string
     */
    public function idPrefixed(): string
    {
        return $this->options->idPrefix . $this->id;
    }

    /**
     * Get or set the rendering layout.
     *
     * @param string|null $layout
     * @return string|static
     */
    public function layout(string $layout = null): string|static
    {
        // Get
        if (is_null($layout)) {
            return $this->layout;
        }
        // Set
        $fromLayout = $this->layout;
        $this->layout = $layout;
        // Changed?
        if ($fromLayout !== $layout) {
            $layoutMethod = 'layout' . ucfirst($layout);
            /**
             * @see LayoutTable::layoutTable()
             */
            if (method_exists($this, $layoutMethod)) {
                return $this->$layoutMethod();
            }
        }
        return $this;
    }

    /**
     * Render the data-model according to the current settings.
     *
     * IMPORTANT NOTES:
     *
     *  - Be very cautious that the returned value might be output as raw HTML,
     *    this method must always sanitize the HTML before returning it.
     *
     *  - Different internal properties will be updated after rendering.
     *
     * (Arguments are inherit form the interface and not used here.)
     *
     * @param array|null $adHocAttrs
     * @param array|null $adHocOptions
     * @return string
     */
    public function render(array $adHocAttrs = null, array $adHocOptions = null): string
    {
        // Prepare the container with non-prefixed ID.
        $container = clone $this->container;
        $container->id(($this->id . $this->options->containerIdSuffix));

        /**
         * Get the contents tag(s) prepared by the layout-trait.
         * @see Renderable::tablePrepared()
         */
        $method = $this->layout() . 'Prepared';
        $contents = $this->$method();

        // Wrap everything with the common container and render the output.
        $naughtyHTML = $container->contents(
            $this->options->prefix ?? '',
            $contents,
            $this->options->suffix ?? '',
        )->render();

        /**
         * Sanitize HTML before output with HTML Purifier, using config for
         * trusted contents.
         *
         * IMPORTANT: user contributed content should be sanitized BEFORE
         * injecting to the column.
         *
         * @see Column::valueTag()
         */
        $config = HTML::purifierConfig(HTML::PURIFY_MODE_TRUSTED);

        /**
         * IDs are removed by default, change the config to allow prefixed IDs.
         * @see http://htmlpurifier.org/docs/enduser-id.html
         */
        $config->set('Attr.EnableID', true);
        $config->set('Attr.IDPrefix', $this->options->idPrefix);

        /**
         * @note Test only here.
         * @see https://stackoverflow.com/questions/28316700/htmlpurifier-add-unsupported-stylesheet-for-one-tag
         *
        $css = $config->getCSSDefinition();
        $css->info['overflow'] = new HTMLPurifier_AttrDef_Enum(array('scroll'));
        $css->info['overflow-x'] = new HTMLPurifier_AttrDef_Enum(array('scroll'));
        $css->info['overflow-y'] = new HTMLPurifier_AttrDef_Enum(array('scroll'));
         */

        // Pure
        $purified = HTML::purify($naughtyHTML, $config);

        // Here we got the pure and maybe beautiful HTML.
        return $this->options->beautifyHTML ? Beautify::html($purified) : $purified;
    }
}