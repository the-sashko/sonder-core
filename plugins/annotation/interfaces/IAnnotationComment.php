<?php

namespace Sonder\Plugins\Annotation\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IAnnotationComment
{
    /**
     * @return string|null
     */
    public function getText(): ?string;
}
