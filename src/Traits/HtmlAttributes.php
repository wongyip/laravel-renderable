<?php

namespace Wongyip\Laravel\Renderable\Traits;

trait HtmlAttributes
{
    use CssClass, CssStyle, GetSetter;
    /**
     * @var string
     */
    protected string $content = '';
//    /**
//     * @var string
//     */
//    protected string $contentHtml = '';
    /**
     * @var string
     */
    protected string $id = '';
    /**
     * @var string
     */
    protected string $tagName = 'div';

    /**
     * @param string|null $setter
     * @return string|$this
     */
    public function content(string $setter = null): string|static
    {
        return $this->getSet(__METHOD__, $setter);
    }

//    /**
//     * N.B. To be rendered with |raw filter, be sure to take care of any XSS attacks.
//     *
//     * @param string|null $setter
//     * @return string|$this
//     */
//    public function contentHtml(string $setter = null): string|static
//    {
//        return $this->getSet(__METHOD__, $setter);
//    }
//
//    /**
//     * @return bool
//     */
//    public function hasHtml(): bool
//    {
//        return !empty($this->contentHtml);
//    }

    /**
     * Get or set id attribute (HtmlTag).
     *
     * Getter return string, setter return static.
     *
     * @param string|null $setter
     * @return string|static
     */
    public function id(string $setter = null): string|static
    {
        return $this->getSet(__METHOD__, $setter);
    }

    /**
     * @param string|null $setter
     * @return string|$this
     */
    public function tagName(string $setter = null): string|static
    {
        return $this->getSet(__METHOD__, $setter);
    }
}