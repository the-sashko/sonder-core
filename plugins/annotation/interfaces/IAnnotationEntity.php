<?php

namespace Sonder\Plugins\Annotation\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IAnnotationEntity
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string|null
     */
    public function getValue(): ?string;
}
