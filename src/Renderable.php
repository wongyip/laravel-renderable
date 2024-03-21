<?php namespace Wongyip\Laravel\Renderable;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use Wongyip\Laravel\Renderable\Components\ColumnRenderable;
use Wongyip\Laravel\Renderable\Traits\Bootstrap4Trait;
use Wongyip\Laravel\Renderable\Traits\PublicPropTrait;
use Wongyip\Laravel\Renderable\Traits\RenderableMacros;
use Wongyip\Laravel\Renderable\Traits\RenderableSetters;
use Wongyip\Laravel\Renderable\Traits\RenderableTrait;
use Wongyip\Laravel\Renderable\Traits\RenderingOptionsTrait;
use Wongyip\Laravel\Renderable\Traits\RenderableTypes;

/**
 * The basic implementation of RenderableInterface.
 */
class Renderable extends RenderableAbstract
{
    use Bootstrap4Trait, PublicPropTrait, RenderableTrait, RenderingOptionsTrait, RenderableSetters;
    use RenderableMacros, RenderableTypes;

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
     * Instantiate a Renderable object (in 'table' layout by default).
     * 
     * @param array $attributes
     * @param true|string|string[] $columns
     * @param string|string[]|null $excluded
     * @param boolean $autoLabels
     * @param string|null $layout
     */
    public function __construct(array $attributes, array|true|string $columns = true, array|string $excluded = null, bool $autoLabels = true, string $layout = null)
    {
        // Preset, related to CSS, change with care.
        $this->containerId = uniqid('mr-');
        
        // Take params
        $this->layout($layout ?? config('renderable.default.layout'));
        $this->attributes($attributes);
        $this->columns($columns);
        $this->exclude($excluded);

        // Automation
        if ($autoLabels) {
            $this->autoLabels();
        }
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
        if (preg_match("/set([A-Z]*)]/", $name, $matches)) {
            $property = lcfirst($matches[1]);
            if (property_exists($this, $property)) {
                $this->$property = $arguments[1];
            }
            else {
                Log::warning(sprintf('Renderable.%s property does not exists.', $property));
            }
            return $this;
        }
        throw new Exception(sprintf('Not implemented %s.%s()', (new ReflectionClass($this))->getShortName(), $name));
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

                $renderable = ColumnRenderable::make(
                    name:      $column,
                    value:     $this->attribute($column),
                    type:      $this->type($column),
                    label:     $this->label($column),
                    labelHTML: $this->labelHTML($column),
                    options:   $this->options($column)
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

    /**
     * {@inheritDoc}
     * @see \Wongyip\Laravel\Renderable\RenderableInterface::value()
     */
    public function value($column)
    {
        Log::warning('Deprecated method called: Renderable.value()');

        // The original value.
        $value = $this->attribute($column);

        // Type defined locally.
        $type = $this->type($column);
        $options = $this->options($column);

        switch ($type) {
            // These types must be an array, so the view could handle it correctly.
            case 'ol':
            case 'ul':
            case 'lines':
                return is_array($value) ? $value : [$value];
            case 'boolean':
                // In case of null and there is a null-replacement.
                if (is_null($value) && key_exists('valueNull', $options)) {
                    return $options['valueNull'];
                }
                // NULL as false now.
                return $value ? $options['valueTrue'] : $options['valueFalse'];
            case 'csv':
                // @todo what if $value is not scalar?
                return is_array($value) ? implode($options['glue'], $value) : $value;
            default:
                // Array to default format.
                if (is_array($value)) {
                    // Output array values as CSV by default.
                    return implode(Renderable::DEFAULT_CSV_GLUE, array_values($value));
                }
                // DateTime to string
                elseif ($value instanceof DateTime) {
                    return $value->format(LARAVEL_RENDERABLE_DATETIME_FORMAT);
                }
                // Boolean to example 'Yes', 'No', etc.
                elseif (is_bool($value)) {
                    return $value ? Renderable::DEFAULT_VALUE_BOOL_TRUE : Renderable::DEFAULT_VALUE_BOOL_FALSE;
                }
        }
        // GIGO
        return $value;
    }
}