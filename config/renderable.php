<?php

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
        'glue'       => ', ',
        'html'       => false,
        'itemClass'  => '',
        'itemStyle'  => '',
        'listClass'  => '',
        'listStyle'  => '',
        'type'       => 'string',
        'valueFalse' => 'No',
        'valueNull'  => '-',
        'valueTrue'  => 'Yes',
    ],
];