<?php

/**
 * Default options.
 *
 * @see https://laravel.com/docs/10.x/packages#configuration
 * @see https://laravel.com/docs/11.x/packages#configuration
 */
return [
    /**
     * @see \Wongyip\Laravel\Renderable\Components\RenderableOptions
     */
    'options' => [
        'beautifyHTML'           => env('RENDERABLE_BEAUTIFY_HTML', false),
        'containerIdSuffix'      => '-container',
        'emptyRecord'            => 'Empty record.',
        'fieldHeader'            => 'Field',
        'gridClassAppend'        => '',
        'gridClassPrepend'       => 'row',
        'idPrefix'               => 'renderable-',
        'prefix'                 => "\n",
        'renderTableHead'        => true,
        'suffix'                 => "\n",
        'tableBordered'          => true,  // table-bordered
        'tableBorderless'        => false,  // table-borderless
        'tableCaptionSide'       => 'top',
        'tableClassAppend'       => '',
        'tableClassBase'         => 'table',
        'tableClassPrepend'      => '',
        'tableHorizontal'        => false,
        'tableHorizontalHeaders' => false,
        'tableHover'             => false, // table-hover
        'tableStriped'           => false, // table-striped
        'valueHeader'            => 'Value',
    ],
    /**
     * @see \Wongyip\Laravel\Renderable\Components\ColumnOptions
     */
    'columnOptions' => [
        'glue'         => ', ',
        'html'         => false,
        'icon'         => '',
        'iconPosition' => 'before', // or 'after'
        'itemClass'    => '',
        'itemStyle'    => '',
        'listClass'    => '',
        'listStyle'    => '',
        'type'         => 'text',
        'valueFalse'   => 'No',
        'valueNull'    => '-',
        'valueTrue'    => 'Yes',
    ],
];