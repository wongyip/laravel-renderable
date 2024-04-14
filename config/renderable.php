<?php

/**
 * @todo Docs.
 */
return [
    /**
     * @see \Wongyip\Laravel\Renderable\Components\RenderableOptions
     */
    'options' => [
        'emptyRecord'      => 'Empty record.',
        'fieldHeader'      => 'Field',
        'valueHeader'      => 'Value',
        'renderTableHead'  => true,
        'tableCaptionSide' => 'top',
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