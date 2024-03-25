<?php

namespace Wongyip\Laravel\Renderable\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;

trait CssClass
{
    /**
     * @var array
     */
    protected array $cssClassesArray = [];

    /**
     * Extract all CSS classes as a space-separated string.
     *
     * @return string
     */
    public function class(): string
    {
        return implode(' ', $this->classes());
    }

    /**
     * Extract all CSS classes as an array.
     *
     * @return string[]|array
     */
    public function classes(): array
    {
        return $this->classesHook($this->cssClassesArray);
    }

    /**
     * [Extension] Modify the CSS classes array before output.
     *
     * @param string[]|array $classes
     */
    protected function classesHook(array $classes): array
    {
        return $classes;
    }

    /**
     * Add (append) a list of CSS classes to the classes array (space=-separated
     * classes list is supported).
     *
     * @param string ...$classes
     * @return static
     */
    public function classAdd(string ...$classes): static
    {
        $classes = $this->__parseClasses($classes);
        // @todo is array_diff() necessary?
        $this->cssClassesArray = array_unique(array_merge($this->cssClassesArray, $classes));
        return $this;
    }

    /**
     * Add (append) a list of CSS classes to the classes array(space=-separated
     * classes list is supported).
     *
     * @param string ...$classes
     * @return static
     */
    public function classAppend(string ...$classes): static
    {
        return $this->classAdd(...$classes);
    }

    /**
     * Prepend a list of CSS classes to the classes array (space=-separated
     * classes list is supported).
     *
     * @param string ...$classes
     * @return static
     *@todo is array_diff() necessary?
     */
    public function classPrepend(string ...$classes): static
    {
        $classes = $this->__parseClasses($classes);
        $this->cssClassesArray = array_unique(array_merge($classes, array_diff($this->cssClassesArray, $classes)));
        // $this->class = empty($this->cssClassesArray) ? '' : implode(' ', $this->cssClassesArray);
        return $this;
    }

    /**
     * Remove a list of CSS classes from the classes array (space=-separated
     * classes list is supported).
     *
     * @param string|array|string[] $classes
     * @return static
     */
    public function classRemove(string ...$classes): static
    {
        $classes = $this->__parseClasses($classes);
        if (!empty($classes)) {
            $this->cssClassesArray = array_unique(array_diff($this->cssClassesArray, $classes));
        }
        return $this;
    }

    /**
     * Parse input into array of CSS classes.
     *
     * @param $classes
     * @return array
     */
    private function __parseClasses($classes): array
    {
        try {
            return array_map('trim', explode(' ', implode(' ', is_array($classes) ? $classes : [$classes])));
        }
        catch (Throwable $e) {
            Log::warning(sprintf('Error in %s (message: %s).', __METHOD__, $e->getMessage()));
            return [];
        }
    }
}