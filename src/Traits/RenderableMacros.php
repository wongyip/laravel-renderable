<?php namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Support\Facades\Log;
use Wongyip\Laravel\Renderable\Renderable;

trait RenderableMacros
{
    /**
     * Get or set the field header style. Setter default replace existing unless
     * $keepExisting is true.
     *
     * Although it could be done by $this->fieldHeader->styleAppend($setter), the
     * advantage of this method is that it returns the Renderable for chaining.
     *
     * @todo Grid layout support to be added.
     *
     * @param string|null $setter
     * @param bool|null $keepExisting
     * @return string|null|static
     */
    public function fieldHeaderStyle(string $setter = null, bool $keepExisting = null): string|null|static
    {
        if ($this->layout !== Renderable::LAYOUT_TABLE) {
            Log::warning('RenderableMacros.fieldHeaderStyle() Required table layout.');
            return is_null($setter) ? null : $this;
        }

        // Get
        if (is_null($setter)) {
            return $this->table->head->row(0)->cell(0)->style();
        }
        // Set
        if (!$keepExisting) {
            // Replace
            $this->table->head->row(0)->cell(0)->styleEmpty();
        }
        $this->table->head->row(0)->cell(0)->styleAppend($setter);
        return $this;
    }
}