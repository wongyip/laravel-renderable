<?php namespace Wongyip\Laravel\Renderable;

interface RenderableInterface
{
    /**
     * Get all original attributes as an associative array. (NO SETTER)
     *
     * @return array
     */
    public function attributes();
    
    /**
     * Get original value of a column. (NO SETTER)
     *
     * @param string $column
     * @return mixed|NULL
     */
    public function attribute($column);
    
    /**
     * Get or set columns that should be rendered (unless specified excluded).
     *
     * Setter:
     *   1. will take all columns of $model->toArray() if $columns is set to TRUE,
     *   2. will merge the columns into existing $columns unless $replace is TRUE.
     *
     * @param string[]|string $columns
     * @param boolean         $replace
     * @return string[]|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function columns($columns = null, $replace = false);
    
    /**
     * Get or set columns that should be rendered with the |raw filter..
     *
     * Setter:
     *   1. will take all columns of $model->toArray() if $columns is set to TRUE,
     *   2. will merge the columns into existing $columns unless $replace is TRUE.
     *
     * @param string[]|string $columns
     * @param boolean         $replace
     * @return string[]|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function columnsHTML($columns = null, $replace = false);
    
    /**
     * Get or set columns that should be excluded from rendering.
     *
     * Setter:
     *   1. will merge the columns into existing $columns unless $replace is TRUE.
     *   2. put an empty array as $excluded and set $replace to TRUE empty the list.
     *
     * @param string[]|string $excluded
     * @param boolean         $replace
     * @return string[]|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function exclude($excluded = null, $replace = false);
    
    /**
     * Get or set the layout.
     *
     * @param string $layout
     * @return string|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function layout($layout = null);
    
    /**
     * Get or set the options array data type of a column, where setter supports
     * an array of columns as input.
     *
     * Note that certain setter methods, e.g. typeBoolean(), is recommended to
     * use when settting data type if there are options bound to that data type.
     *
     * Setter will merge $options into existing options array unless $replace is TRUE.
     *
     * @param string|string[] $column
     * @param array           $options
     * @param boolean $replace
     * @return array|\Wongyip\Laravel\Renderable\ModelRenderable
     */
    public function options($column, $options = null, $replace = false);
    
    /**
     * Get an array of compiled ColumnRederable objects for rendering.
     *
     * @return \Wongyip\Laravel\Renderable\ColumnRenderable[]
     */
    public function renderables();
    
    /**
     * Get the parsed value of a column. (NO SETTER)
     *
     * @param string $column
     * @return mixed|NULL
     */
    public function value($column);
    
    /**
     * Get the view with the renderable marco, e.g. 'renderable::table'.
     *
     * @return string
     */
    public function view();
}