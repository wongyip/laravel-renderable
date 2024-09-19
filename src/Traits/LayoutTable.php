<?php namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Database\Eloquent\Model;
use Wongyip\HTML\Interfaces\RendererInterface;
use Wongyip\HTML\RawHTML;
use Wongyip\HTML\Table;
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
     * The main tag of the Renderable object.
     *
     * Notes:
     *  1. Value of the ID attribute is ignored on render.
     *  2. Table's class list will be emptied on render.
     *  3. THead will likely be modified on render depends on various options.s
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
        );

        // @todo Broken now, border glitch at bottom-left corner.
        // $this->container->classAdd(Bootstrap::classTableResponsive());
        return $this;
    }

    /**
     * Instantiate a Renderable object in table layout.
     *
     * @param array|Model $attributes Source attributes array or Eloquent Model.
     * @param array|string[]|string|bool|null $included Default null for none, set TRUE to include all columns, string or array to include column(s) explicitly.
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
     * @param array|string[]|string|bool|null $included Default null for none, set TRUE to include all columns, string or array to include column(s) explicitly.
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
     * @return RendererInterface
     * @see Renderable::render() Caller
     */
    public function tablePrepared(): RendererInterface
    {
        // Columns included (names).
        $included = $this->include();

        // No?
        if (empty($included)) {
            $html = sprintf('<p><em>%s</em></p>', htmlspecialchars($this->options->emptyRecord));
            return RawHTML::create($html);
        }

        // Less $this...
        $options     = $this->options;
        $table       = $this->table;

        // Setting things up
        $table
            ->id($this->id)
            ->classEmpty()
            ->class(
                $options->tableClassPrepend,
                Renderable::CSS_CLASS_TABLE,
                $options->tableClassBase,
                $options->tableBorderless ? 'table-borderless' : '',
                $options->tableBordered ? 'table-bordered' : '',
                $options->tableStriped ? 'table-striped' : '',
                $options->tableHover ? 'table-hover' : '',
                $options->tableClassAppend
            );

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
                name:    $name,
                value:   $this->attribute($name),
                label:   $this->label($name),
                options: $this->columnOptions($name)
            );
        }

        // Vertical (default).
        if (!$options->tableHorizontal) {

            // THead
            if ($headers = $this->columnHeaders()) {
                $cells = count($headers) === 1
                    ? [TH::create($headers[0])->attribute('colspan', 2),]
                    : [TH::create($headers[0]), TH::create($headers[1])];
                $table->head->addRows(TR::create(...$cells));
            }

            // Fill table body with rows of data-columns.
            foreach ($columns as $name => $column) {
                $table->body->addRows(
                    TR::create(
                        $column->labelTag('th')->style('width', $this->options->tableLabelCellWidth ?? 'auto'),
                        $column->valueTag('td')
                    )
                    ->class('field-' . $name)
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