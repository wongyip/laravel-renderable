<?php namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Database\Eloquent\Model;
use Wongyip\HTML\RawHTML;
use Wongyip\HTML\Table;
use Wongyip\HTML\TagAbstract;
use Wongyip\HTML\TBody;
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
     * attribute is ignored by the render() method, and body and head will be
     * emptied on render.
     *
     * Notes:
     *
     *  1. The THead maybe modified on render() depends on the renderTableHead
     *     option.
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
        // Create a table with empty thead and tbody.
        $this->table = Table::create(
            THead::create()->class(Renderable::CSS_CLASS_TABLE_HEAD),
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
     * Instantiate a Renderable object in table layout, with fields rendered
     * horizontally. Where "Field" and "Value" header cells are NOT rendered
     * unless options.tableHorizontalHeaders is TRUE
     *
     * @param array|Model $attributes Source attributes array or Eloquent Model.
     * @param array|string[]|string|bool|null $included Default true for all columns.
     * @param array|string[]|string|null $excluded Default null for none.
     * @param array|RenderableOptions|null $options Custom options, skip to taking values from config('renderable.options').
     * @return static
     */
    static function tableHorizontal(array|Model $attributes, array|string|bool $included = null, array|string $excluded = null, array|RenderableOptions $options = null): static
    {
        $r = static::table($attributes, $included, $excluded, $options);
        $r->options->tableHorizontal = true;
        return $r;
    }

    /**
     * Get the main tag ready to for rendering.
     *
     * @return TagAbstract
     */
    public function tablePrepared(): TagAbstract
    {
        // Columns included (names).
        $included = $this->include();

        // No?
        if (empty($included)) {
            $html = sprintf('<p><em>%s</em></p>', htmlspecialchars($this->options->emptyRecord));
            return RawHTML::create($html);
        }

        // Get rid of $this...
        $options     = $this->options;
        $table       = $this->table;
        $fieldHeader = $this->fieldHeader;
        $valueHeader = $this->valueHeader;

        // Setting things up
        $table->id($this->id);
        $fieldHeader->tagName('th')->contentsEmpty()->contents($options->fieldHeader);
        $valueHeader->tagName('th')->contentsEmpty()->contents($options->valueHeader);

        // Position the caption if set.
        if ($table->hasCaption()) {
            $table->caption->styleProperty('caption-side', $options->tableCaptionSide, true);
        }

        // Empty before filling.
        $table->head->contentsEmpty();
        $table->body->contentsEmpty();

        // Prepare Column objects.
        $columns = [];
        foreach ($included as $name) {
            $columns[$name] = new Column(
                name:      $name,
                value:     $this->attribute($name),
                label:     $this->label($name),
                labelHTML: $this->labelHTML($name) ?? '',
                options:   $this->columnOptions($name)
            );
        }

        // Vertical (default).
        if (!$options->tableHorizontal) {
            // Show or hide table head by option.
            if ($options->renderTableHead) {
                if (is_a($table->head, THead::class)) {
                    $table->head->addRows(
                        TR::create($fieldHeader, $valueHeader)
                    );
                }
                else {
                    /**
                     * While previously prepared with renderTableHead: FALSE.
                     * @todo To be reviewed.
                     */
                    $table->head = THead::create(TR::create($fieldHeader, $valueHeader))
                        ->class(Renderable::CSS_CLASS_TABLE_HEAD);
                }
            }
            // Fill table body with rows of data-columns.
            foreach ($columns as $name => $column) {
                $table->body->addRows(
                    TR::create($column->labelTag('th'), $column->valueTag('td'))->class('field-' . $name)
                );
            }
        }
        // Horizontal
        else {
            $rowHead = $options->tableHorizontalHeaders ? TR::create($fieldHeader) : TR::create();
            $rowBody = $options->tableHorizontalHeaders ? TR::create($valueHeader) : TR::create();
            // Fill table head and body with data-columns.
            foreach ($columns as $name => $column) {
                $rowHead->addCells($column->labelTag('th')->classAppend('field-' . $name));
                $rowBody->addCells($column->valueTag('td')->classAppend('field-' . $name));
            }
            $table->head->addRows($rowHead);
            $table->body->addRows($rowBody);
        }
        // Yo
        return $table;
    }
}