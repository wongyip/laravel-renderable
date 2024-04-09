<?php namespace Wongyip\Laravel\Renderable;

use Exception;
use HTMLPurifier_Config;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Wongyip\HTML\Beautify;
use Wongyip\HTML\Tag;
use Wongyip\HTML\TagAbstract;
use Wongyip\Laravel\Renderable\Components\RenderableOptions;
use Wongyip\Laravel\Renderable\Traits\Bootstrap4Trait;
use Wongyip\Laravel\Renderable\Traits\LayoutGrid;
use Wongyip\Laravel\Renderable\Traits\RenderableAttributes;
use Wongyip\Laravel\Renderable\Traits\RenderableColumns;
use Wongyip\Laravel\Renderable\Traits\RenderableLabels;
use Wongyip\Laravel\Renderable\Traits\RenderableMacros;
use Wongyip\Laravel\Renderable\Traits\LayoutTable;
use Wongyip\Laravel\Renderable\Traits\RenderableTypes;
use Wongyip\Laravel\Renderable\Utils\HTML;

/**
 * @method string|Renderable id(string $setter = null)
 * @method string|Renderable idPrefix(string $setter = null)
 */
class Renderable implements RenderableInterface
{
    use RenderableAttributes, RenderableColumns, RenderableLabels, RenderableTypes;

    // @todo Review needed.
    use Bootstrap4Trait;

    // New
    use LayoutGrid, LayoutTable, RenderableMacros;

    const CONTAINER_ID_SUFFIX  = '-container';
    const CSS_CLASS_BODY       = 'renderable-body';
    const CSS_CLASS_CONTAINER  = 'renderable-container';
    const CSS_CLASS_LABEL      = 'renderable-label';
    const CSS_CLASS_TABLE_HEAD = 'thead-light';
    const CSS_CLASS_VALUE      = 'renderable-value';
    const LAYOUT_DEFAULT       = 'table';
    const LAYOUT_TABLE         = 'table';
    const LAYOUT_GRID          = 'grid';

    /**
     * The ID attribute of the main renderable HTML tag.
     * N.B. This ID will be prefixed with $idPrefix on render().
     *
     * @var string
     */
    protected string $id;
    /**
     * The ID Prefix for ALL generated tags having ID attribute.
     *
     * @see self::render()
     * @var string
     */
    protected string $idPrefix = 'renderable-';
    /**
     * Layout for view lookup.
     *
     * @var string
     */
    protected string $layout = '';
    /**
     * The container of the main tag. Note that runtime value of the ID attribute
     * is ignored by the render() method.
     *
     * @var TagAbstract
     */
    public TagAbstract $container;
    /**
     * Options and switches.
     *
     * @var RenderableOptions
     */
    public RenderableOptions $options;

    /**
     * The Renderable object.
     *
     * 1. Note to the $options argument:
     *     - NULL: take all defaults from config('renderable.options').
     *     - RenderableOptions object: ignore defaults.
     *     - Array: merge into default options.
     *
     * 2. Note for the $attributes argument:
     *     - Array: simply set the $attributes array.
     *     - Eloquent model: takes output of toArray() as $attributes, where
     *       hidden attributes and not appended accessors/mutators are not
     *       taken. However, they will be rendered if they're "$included"
     *       and not "$excluded".
     *
     * @param array|Model $attributes Source attributes array or Eloquent Model.
     * @param array|string[]|string|null $included Default true for all columns.
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

        // Wrapper (no need to set ID).
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
        // Get-setters
        if (in_array($name, ['id', 'idPrefix'])) {
            if (isset($arguments[0])) {
                $this->$name = $arguments[0];
                return $this;
            }
            return $this->$name;
        }
        throw new Exception(sprintf('Undefined method "%s.%s()" called.', (new ReflectionClass($this))->getShortName(), $name));
    }

    /**
     * The ID of the main tag rendered, take this if a selector is needed for
     * scripting or styling.
     *
     * @return string
     */
    private function idPrefixed(): string
    {
        return $this->idPrefix . $this->id;
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
     * IMPORTANT: be very cautious that the returned value might be output as
     * raw HTML, this method must always sanitize the HTML before returning it.
     *
     * @return string
     * @see Renderable::tablePrepared()
     */
    public function render(): string
    {
        // Prepare the container with non-prefixed ID.
        $container = clone $this->container;
        $container->id(($this->id . static::CONTAINER_ID_SUFFIX));

        // Get the contents tag(s) prepared by the layout-trait.
        $method = $this->layout() . 'Prepared';
        $contents = $this->$method();

        // Wrap it with the common container and render the output.
        $naughtyHTML = $container->contents($contents)->render();

        /**
         * Sanitize HTML before output, with HTML Purifier (default config).
         * IDs are removed by default, change the config to allow prefixed IDs.
         * @see http://htmlpurifier.org/docs/enduser-id.html
         */
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Attr.EnableID', true);
        $config->set('Attr.IDPrefix', $this->idPrefix);
        $purified = HTML::purify($naughtyHTML, $config);

        // Here we got the pure and beautiful HTML.
        return "\n" . Beautify::init()->beautify($purified) . "\n";
    }
}