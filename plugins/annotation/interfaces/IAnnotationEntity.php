<?php

namespace Sonder\Plugins\Annotation\Interfaces;

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
