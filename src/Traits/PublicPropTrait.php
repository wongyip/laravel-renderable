<?php namespace Wongyip\Laravel\Renderable\Traits;

/**
 * All simple, strict forward options, properties, etc.
 * 
 * @author wongyip
 */
trait PublicPropTrait
{
    /**
     * @var string
     */
    public $captionField = 'Field';
    /**
     * @var string
     */
    public $captionValue = 'Value';
    /**
     * @var string
     */
    public $containerClass;
    /**
     * @var string
     */
    public $containerId;
    /**
     * @var string
     */
    public $containerStyle;
    /**
     * @var string
     */
    public $fieldClass;
    /**
     * @var string
     */
    public $fieldHeaderClass;
    /**
     * @var string
     */
    public $fieldHeaderStyle;
    /**
     * @var string
     */
    public $fieldStyle;
    /**
     * @var string
     */
    public $tableCaption;
    /**
     * @var string
     */
    public $tableClass = 'table table-bordered table-hover';
    /**
     * @var string
     */
    public $tableStyle;
    /**
     * @var string
     */
    public $tableHeadClass = 'thead-light';
    /**
     * @var string
     */
    public $tableResponsive;
    /**
     * @var string
     */
    public $tableHeadStyle;
    /**
     * @var string
     */
    public $tableLabelCellTag = 'th';
    /**
     * @var string
     */
    public $labelClass;
    /**
     * @var string
     */
    public $labelStyle;
    /**
     * @var string
     */
    public $valueClass;
    /**
     * @var string
     */
    public $valueHeaderClass;
    /**
     * @var string
     */
    public $valueHeaderStyle;
    /**
     * @var string
     */
    public $valueStyle;
}