<?php namespace Wongyip\Laravel\Renderable\Traits;


/**
 * 1. Everything related to table-layout should go here.
 * 2. Only properties with a parsed values are having getter function.
 *
 * @see /views/table.twig
 */
trait RenderableTable
{
    /**
     * @var string
     */
    public string $tableCaption = '';
    /**
     * @var string
     */
    public string $tableHeadClass = 'thead-light';
    /**
     * @var string
     */
    public string $tableResponsive = '';
    /**
     * @var string
     */
    public string $tableHeadStyle = '';
    /**
     * @var string
     */
    public string $tableLabelCellTag = 'th';

    /**
     * @deprecated
     * @var string
     */
    public string $tableClass = 'deprecated__please_use_R_dot_class_function';
    /**
     * @deprecated
     * @var string
     */
    public string $tableStyle = 'deprecated: please use R.style();';

    /**
     * @param array $classes
     * @return array
     */
    public function classHookTable(array $classes): array
    {
        array_push($classes, 'renderable-table', 'table', 'table-bordered', 'table-hover');
        return $classes;
    }
}