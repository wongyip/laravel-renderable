<?php namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Database\Eloquent\Model;
use Wongyip\HTML\RawHTML;
use Wongyip\HTML\Table;
use Wongyip\HTML\TagAbstract;
use Wongyip\HTML\TBody;
use Wongyip\HTML\TH;
use Wongyip\HTML\THead;
use Wongyip\HTML\TR;
use Wongyip\Laravel\Renderable\Components\Column;
use Wongyip\Laravel\Renderable\Components\RenderableOptions;
use Wongyip\Laravel\Renderable\Renderable;

/**
 * Everything related to table-layout should go here.
 *
 * @see /views/table.twig
 */
trait LayoutTable
{
    /**
     * The main tag of the Renderable object. Note that runtime value of the ID
     * attribute is ignored by the render() method.
     *
     * @var Table
     */
    public Table $table;

    /**
     * Set up table layout, on instantiate or layout changed by calling the
     * layout() method.
     *
     * @see Renderable::layout()
     * @return static
     */
    protected function layoutTable(): static
    {
        $this->table = Table::create(
                THead::create(
                    TR::create(
                        TH::create($this->options->fieldHeader),
                        TH::create($this->options->valueHeader)
                    )
                )->class(Renderable::CSS_CLASS_TABLE_HEAD),
                TBody::create()->class(Renderable::CSS_CLASS_BODY)
            )->class('renderable-table', 'table', 'table-bordered', 'table-hover');

        // @todo Broken now, border glitch at bottom-left corner.
        // $this->container->classAdd(Bootstrap::classTableResponsive());
        return $this;
    }

    /**
     * Instantiate a Renderable object in table layout.
     *
     * @param array|Model $attributes Source attributes array or Eloquent Model.
     * @param array|string[]|string|bool|null $included Default true for all columns.
     * @param array|string[]|string|null $excluded Default null for none.
     * @param array|RenderableOptions|null $options Custom options, skip to taking values from config('renderable.options').
     * @return static
     */
    static function table(array|Model $attributes, array|string|bool $included = null, array|string $excluded = null, array|RenderableOptions $options = null): static
    {
        return new static($attributes, $included, $excluded, $options, Renderable::LAYOUT_TABLE);
    }

    /**
     * Prepare the table tag with child tags of all columns to be rendered.
     *
     * @return TagAbstract
     */
    public function tablePrepared(): TagAbstract
    {
        if ($included = $this->include()) {
            /**
             * Clone the table tag for preparation, assign ID to the cloned tag
             * and empty its body.
             */
            $table = clone $this->table;
            $table->id($this->id);
            $table->body->contentsEmpty();

            // Fill up its body with columns to be rendered.
            foreach ($included as $name) {

                // Position the caption if set.
                if ($table->hasCaption()) {
                    $table->caption->styleAppend('caption-side: ' . $this->options->tableCaptionSide);
                }
                // Label and value tags.
                $column = new Column(
                    name:      $name,
                    value:     $this->attribute($name),
                    label:     $this->label($name),
                    labelHTML: $this->labelHTML($name) ?? '',
                    options:   $this->columnOptions($name)
                );
                $labelCell = $column->labelTag('th');
                $valueCell = $column->valueTag('td');
                $row = TR::create($labelCell, $valueCell)->class('field-' . $name);
                $table->body->addRows($row);
            }

            /**
             * Apply rendering options.
             * @see RenderingOptionsTra                                                                    it
             */
            if (!$this->options->renderTableHead) {
                unset($table->head);
            }

            return $table;
        }
        $html = sprintf('<p><em>%s</em></p>', htmlspecialchars($this->options->emptyRecord));
        return RawHTML::create($html);
    }
}