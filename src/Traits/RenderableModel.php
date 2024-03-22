<?php namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Database\Eloquent\Model;
use Wongyip\Laravel\Renderable\Renderable;

trait RenderableModel
{
    /**
     * Return the model input model.
     *
     * @note-only Change to the model after instantiation is not a good idea.
     *
     * @return Model|null
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }
}