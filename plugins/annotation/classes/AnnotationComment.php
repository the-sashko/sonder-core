<?php

namespace Sonder\Plugins\Annotation\Classes;

use Sonder\Plugins\Annotation\Interfaces\IAnnotationComment;

#[IAnnotationComment]
final class AnnotationComment implements IAnnotationComment
{
    /**
     * @var string|null
     */
    private ?string $_text;

    /**
     * @param string|null $text
     */
    public function __construct(?string $text = null)
    {
        $this->_text = $this->_getFormattedText($text);
    }

    /**
     * @return string|null
     */
    final public function getText(): ?string
    {
        return $this->_text;
    }

    /**
     * @param string|null $text
     * @return string|null
     */
    private function _getFormattedText(?string $text = null): ?string
    {
        if (empty($text)) {
            return null;
        }

        $text = preg_replace('/\s+/u', ' ', $text);
        $text = preg_replace('/(^(\s|)[*]+)/u', '', $text);
        $text = preg_replace('/(^\s)|(\s$)/u', '', $text);
        $text = preg_replace('/(^\/\*\*)|(^([*]+|)\/)/u', '', $text);
        $text = preg_replace('/(^\s)|(\s$)/u', '', $text);
        $text = preg_replace('/(^[*]+)|([*]+$)/u', '', $text);

        return !empty($text) ? $text : null;
    }
}
