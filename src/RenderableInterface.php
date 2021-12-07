<?php namespace Wongyip\Laravel\Renderable;

interface RenderableInterface
{
    public function attributes();
    public function columns($columns = null, $replace = false);
    public function columnsHTML($columns = null, $replace = false);
    public function exclude($excluded = null, $replace = false);
    public function layout($layout = null);
    public function options($column, $options = null, $replace = false);
    public function renderables();
    public function value($column);
    public function view();
}