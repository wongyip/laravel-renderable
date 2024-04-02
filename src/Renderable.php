<?php namespace Wongyip\Laravel\Renderable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;
use Wongyip\HTML\Tag;
use Wongyip\Laravel\Renderable\Components\ColumnRenderable;
use Wongyip\Laravel\Renderable\Traits\Bootstrap4Trait;
use Wongyip\Laravel\Renderable\Traits\CommonProperties;
use Wongyip\Laravel\Renderable\Traits\RenderableMacros;
use Wongyip\Laravel\Renderable\Traits\LayoutTable;
use Wongyip\Laravel\Renderable\Traits\RenderableTrait;
use Wongyip\Laravel\Renderable\Traits\RenderableTypes;
use Wongyip\Laravel\Renderable\Traits\RenderingOptionsTrait;

/**
 * The basic implementation of RenderableInterface.
 *
 * @method Renderable asTable()
 * @method Renderable asGrid()
 */
class Renderable extends RenderableAbstract
{
    // @todo Review needed.
    use Bootstrap4Trait, CommonProperties, RenderableTrait, RenderingOptionsTrait;

    // New
    use LayoutTable, RenderableMacros, RenderableTypes;

    /**
     * Simple single table layout, with two columns (Field & Value).
     *
     * @var string
     */
    const LAYOUT_TABLE = 'table';
    /**
     * Columns-rows based grid system, like Bootstrap.
     *
     * @var string
     */
    const LAYOUT_GRID  = 'grid';
    /**
     * @var Tag
     */
    public Tag $body;
    /**
     * @var Tag
     */
    public Tag $container;
    /**
     * @var Tag
     */
    public Tag $fieldHeader;
    /**
     * @var Tag
     */
    public Tag $valueHeader;

    /**
     * The Renderable object.
     *
     * @param array|Model $attributes Input attributes, also accepts Eloquent Model.
     * @param array|true|string $columns Default true for all columns.
     * @param array|string|null $excluded Default null for none.
     * @param string|null $layout Default to config('renderable.default.layout').
     */
    public function __construct(array|Model $attributes, array|true|string $columns = true, array|string $excluded = null, string $layout = null)
    {
        // Init.
        $this->body = Tag::make()->id('r' . crc32(uniqid()));

        // Take attributes.
        if ($attributes instanceof Model) {
            $this->model = $attributes;
            $this->attributes($this->model->toArray());
        }
        else {
            $this->attributes = $attributes;
        }

        // Components
        $this->container = Tag::make('div')->id($this->body->id() . '-container')->classAdd('renderable-object-container');
        $this->fieldHeader = Tag::make()->contents('Field');
        $this->valueHeader = Tag::make()->contents('Value');

        // Take other params.
        $this->layout($layout ?? config('renderable.default.layout'));
        $this->columns($columns);
        $this->exclude($excluded);

        // Automation
        $this->autoLabels();
    }

    /**
     * @note Work in progress.
     * @param string $name
     * @param array $arguments
     * @return Renderable
     * @throws Exception
     */
    public function __call(string $name, array $arguments)
    {
        /**
         * e.g. $this->setSomeProperty($value) will be handled by $this->setter('someProperty', $value)
         */
        if (preg_match("/^set([A-Z].*)/", $name, $matches)) {
            $property = lcfirst($matches[1]);
            if (property_exists($this, $property)) {
                $this->$property = $arguments[1];
            }
            else {
                Log::warning(sprintf('Renderable.%s property does not exists.', $property));
            }
            return $this;
        }
        /**
         * @see Renderable::asGrid()
         * @see Renderable::asTable();
         */
        elseif (preg_match("/^as([A-Z].*)/", $name, $matches)) {
            return $this->renderAs(Str::kebab($matches[1]));
        }

        Log::debug(sprintf('Method %s.%s() is not implemented, return NULL.', (new ReflectionClass($this))->getShortName(), $name));
        return null;
    }

    /**
     * Alias to $this->columns().
     *
     * @param array|string|bool|null $columns
     * @param bool $replace
     * @return array|static
     */
    public function include(array|string|bool $columns = null, bool $replace = false): array|static
    {
        return $this->columns($columns, $replace);
    }

    /**
     * @inheritDoc
     */
    public function renderables(): array
    {
        $renderables = [];
        if ($columns = $this->columns()) {
            foreach ($columns as $column) {

                $renderable = new ColumnRenderable(
                    name:      $column,
                    value:     $this->attribute($column),
                    type:      $this->type($column),
                    label:     $this->label($column),
                    labelHTML: $this->labelHTML($column),
                    options:   $this->columnOptions($column)
                );

                if ($renderable->isRenderable()) {
                    $renderables[] = $renderable;
                }
                else {
                    Log::warning(
                        sprintf('ColumnRenderable composed for column [%s] is not renderable (possible nested array).', $column)
                    );
                }
            }
        }
        return $renderables;
    }
}