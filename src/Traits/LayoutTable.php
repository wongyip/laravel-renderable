<?php namespace Wongyip\Laravel\Renderable\Traits;


use Wongyip\HTML\Table;
use Wongyip\HTML\Tag;
use Wongyip\HTML\TagAbstract;
use Wongyip\HTML\TBody;
use Wongyip\HTML\TH;
use Wongyip\HTML\THead;
use Wongyip\HTML\TR;
use Wongyip\Laravel\Renderable\Components\RawHTML;
use Wongyip\Laravel\Renderable\Components\Column;
use Wongyip\Laravel\Renderable\Renderable;

/**
 * 1. Everything related to table-layout should go here.
 * 2. Only properties with a parsed values have their getter function.
 *
 * @see /views/table.twig
 */
trait LayoutTable
{
    /**
     * @var Table
     */
    public Table $table;

    /**
     * @return static
     */
    protected function layoutTable(): static
    {
        $this->table = Table::create(
                THead::create(TR::create(TH::create('Field'), TH::create('Value')))
                    ->class(Renderable::CSS_CLASS_TABLE_HEAD),
                TBody::create()
                    ->class(Renderable::CSS_CLASS_BODY),
                'Details'
            )
            ->id($this->id)
            ->class('renderable-table', 'table', 'table-bordered', 'table-hover');

//        $this->table->fieldHeader->contents('Field');
//        $this->table->valueHeader->contents('Value');

        // @todo Broken now, border glitch at bottom-left corner.
        // $this->container->classAdd(Bootstrap::classTableResponsive());

        return $this;
    }

    /**
     * Prepare the table tag with child tags of all columns to be rendered.
     *
     * @return TagAbstract
     */
    public function tablePrepared(): TagAbstract
    {
        if ($columns = $this->columns()) {
            $this->table->body->contentsEmpty();
            foreach ($columns as $name) {
                $column = Column::init(
                    name:      $name,
                    value:     $this->attribute($name),
                    label:     $this->label($name),
                    labelHTML: $this->labelHTML($name) ?? '',
                    options:   $this->columnOptions($name)
                );
                $labelCell = $column->labelTag('th');
                $valueCell = $column->valueTag('td');
                $this->table->body->addRows(TR::create($labelCell, $valueCell));
            }
            return $this->table;
        }
        $html = sprintf('<p><em>%s</em></p>', htmlspecialchars(config('renderable.default.emptyRecord', 'Empty Records.')));
        return RawHTML::create($html);
    }
}