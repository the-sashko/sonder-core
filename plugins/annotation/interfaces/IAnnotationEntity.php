<?php
namespace Core\Plugins\Annotation\Interfaces;

interface IAnnotationEntity
{
    public function getName(): string;

    public function getValue(): ?string;
}
