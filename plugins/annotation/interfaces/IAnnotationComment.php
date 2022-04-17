<?php

namespace Sonder\Plugins\Annotation\Interfaces;

interface IAnnotationComment
{
    /**
     * @return string|null
     */
    public function getComment(): ?string;
}
