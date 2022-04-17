<?php

namespace Sonder\Plugins\Annotation\Classes;

use Sonder\Plugins\Annotation\Interfaces\IAnnotationComment;

final class AnnotationComment implements IAnnotationComment
{
    /**
     * @var string|null
     */
    private ?string $_comment;

    /**
     * @param string|null $comment
     */
    public function __construct(?string $comment = null)
    {
        $this->_comment = $this->_getFormattedComment($comment);
    }

    /**
     * @return string|null
     */
    final public function getComment(): ?string
    {
        return $this->_comment;
    }

    /**
     * @param string|null $comment
     *
     * @return string|null
     */
    private function _getFormattedComment(?string $comment = null): ?string
    {
        if (empty($comment)) {
            return null;
        }

        $comment = preg_replace('/\s+/su', ' ', $comment);
        $comment = preg_replace('/(^(\s|)[*]+)/su', '', $comment);
        $comment = preg_replace('/(^\s)|(\s$)/su', '', $comment);
        $comment = preg_replace('/(^\/\*\*)|(^([*]+|)\/)/su', '', $comment);
        $comment = preg_replace('/(^\s)|(\s$)/su', '', $comment);
        $comment = preg_replace('/(^[*]+)|([*]+$)/su', '', $comment);

        return !empty($comment) ? $comment : null;
    }
}
