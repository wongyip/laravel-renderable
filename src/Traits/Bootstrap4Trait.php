<?php namespace Wongyip\Laravel\Renderable\Traits;

use Wongyip\PHPHelpers\CSS;

/**
 * @author wongyip
 */
trait Bootstrap4Trait
{
    /**
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableBordered($switch)
    {
        return $this->modifyClass('tableClass', 'table-bordered', $switch);
    }
    
    /**
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableBorderless($switch)
    {
        return $this->modifyClass('tableClass', 'table-borderless', $switch);
    }
    
    /**
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableDark()
    {
        return $this->modifyClass('tableClass', 'table-light', false)->modifyClass('table-dark', true);
    }
    
    /**
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableHover($switch)
    {
        return $this->modifyClass('tableClass', 'table-hover', $switch);
    }
    
    /**
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableLight()
    {
        return $this->modifyClass('tableClass', 'table-dark', false)->modifyClass('table-light', true);
    }
    
    /**
     * Make the talbe scrollable horizontally across every breakpoint while x-overflow.
     *
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableResponsive($switch)
    {
        $all = ['table-responsive', 'table-responsive-sm', 'table-responsive-md', 'table-responsive-lg', 'table-responsive-zl'];
        $this->modifyClass('tableResponsive', $all, false);
        if ($switch === true) {
            $this->modifyClass('tableResponsive', 'table-responsive', $switch);
        }
        return $this;
    }
    
    /**
     * Responsive table at maximum breakpoint SM.
     *
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableResponsiveSM($switch)
    {
        $all = ['table-responsive', 'table-responsive-sm', 'table-responsive-md', 'table-responsive-lg', 'table-responsive-zl'];
        $this->modifyClass('tableResponsive', $all, false);
        if ($switch === true) {
            $this->modifyClass('tableResponsive', 'table-responsive-sm', $switch);
        }
        return $this;
    }
    
    /**
     * Responsive table at maximum breakpoint MD.
     *
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableResponsiveMD($switch)
    {
        $all = ['table-responsive', 'table-responsive-sm', 'table-responsive-md', 'table-responsive-lg', 'table-responsive-zl'];
        $this->modifyClass('tableResponsive', $all, false);
        if ($switch === true) {
            $this->modifyClass('tableResponsive', 'table-responsive-md', $switch);
        }
        return $this;
    }
    
    /**
     * Responsive table at maximum breakpoint LG.
     *
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableResponsiveLG($switch)
    {
        $all = ['table-responsive', 'table-responsive-sm', 'table-responsive-md', 'table-responsive-lg', 'table-responsive-zl'];
        $this->modifyClass('tableResponsive', $all, false);
        if ($switch === true) {
            $this->modifyClass('tableResponsive', 'table-responsive-lg', $switch);
        }
        return $this;
    }
    
    /**
     * Responsive table at maximum breakpoint XL.
     *
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableResponsiveXL($switch)
    {
        $all = ['table-responsive', 'table-responsive-sm', 'table-responsive-md', 'table-responsive-lg', 'table-responsive-zl'];
        $this->modifyClass('tableResponsive', $all, false);
        if ($switch === true) {
            $this->modifyClass('tableResponsive', 'table-responsive-xl', $switch);
        }
        return $this;
    }
    
    /**
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableSM($switch)
    {
        return $this->modifyClass('tableClass', 'table-sm', $switch);
    }
    
    /**
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTableStriped($switch)
    {
        return $this->modifyClass('tableClass', 'table-striped', $switch);
    }
    
    /**
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTHeadDark()
    {
        return $this
            ->modifyClass('tableHeadClass', 'thead-light', false)
            ->modifyClass('tableHeadClass', 'thead-dark', true);
    }
    
    /**
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    public function bsTHeadLight()
    {
        return $this
            ->modifyClass('tableHeadClass', 'thead-dark', false)
            ->modifyClass('tableHeadClass', 'thead-light', true);
    }
    
    /**
     * @param string  $property
     * @param string  $class
     * @param boolean $switch
     * @return \Wongyip\Laravel\Renderable\Renderable
     */
    protected function modifyClass($property, $class, $switch)
    {
        $this->$property = CSS::classAttribute($this->$property, $switch ? $class : null, $switch ? null : $class);
        return $this;
    }
}