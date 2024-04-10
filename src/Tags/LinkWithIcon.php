<?php

namespace Wongyip\Laravel\Renderable\Tags;
//
//use Exception;
//use Wongyip\HTML\Anchor;
//use Wongyip\HTML\TagAbstract;
//use Wongyip\Laravel\Renderable\Components\ColumnOptions;
//
///**
// * @method string|null|LinkWithIcon icon(string $icon = null, string $iconPosition = null)
// */
//class LinkWithIcon extends Anchor
//{
//    protected string $icon;
//    protected string $iconPosition;
//
//    /**
//     * @param string $name
//     * @param array $arguments
//     * @return $this|bool|string|LinkWithIcon|TagAbstract|null
//     * @throws Exception
//     */
//    public function __call(string $name, array $arguments)
//    {
//        // Get or set icon.
//        if ($name === 'icon') {
//            if (isset($arguments[0])) {
//                $this->icon = $arguments[0];
//                if (isset($arguments[1])) {
//                    $this->iconPosition = $arguments[1];
//                }
//                return $this;
//            }
//            return $this->icon ?? null;
//        }
//        return parent::__call($name, $arguments);
//    }
//
//    protected function contentsSuffixed(): array
//    {
//        if ($this->icon && $this->iconPosition === ColumnOptions::ICON_POSITION_AFTER) {
//            return [Icon::create($this->icon)];
//        }
//        return [];
//    }
//
//    protected function contentsPrefixed(): array
//    {
//        if ($this->icon && $this->iconPosition === ColumnOptions::ICON_POSITION_BEFORE) {
//            return [Icon::create($this->icon)];
//        }
//        return [];
//    }
//
//}