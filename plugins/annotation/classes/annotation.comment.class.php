<?php
namespace Core\Plugins\Annotation\Classes;

use Core\Plugins\Annotation\Interfaces\IAnnotationComment;

class AnnotationComment implements IAnnotationComment
{
    private $_comment = null;

    public function __construct(?string $comment = null)
    {
        $this->_comment = $this->_getFormatedComment($comment);
    }

    public function getComment(): ?string
    {
        return $this->_comment;
    }

    private function _getFormatedComment(?string $comment = null): ?string
    {
        if (empty($comment)) {
            return null;
        }

        $comment = preg_replace('/\s+/su', ' ', $comment);
        $comment = preg_replace('/(^(\s|)[\*]+)/su', '', $comment);
        $comment = preg_replace('/(^\s)|(\s$)/su', '', $comment);
        $comment = preg_replace('/(^\/\*\*)|(^([\*]+|)\/)/su', '', $comment);
        $comment = preg_replace('/(^\s)|(\s$)/su', '', $comment);
        $comment = preg_replace('/(^[\*]+)|([\*]+$)/su', '', $comment);

        return !empty($comment) ? $comment : null;
    }
}
