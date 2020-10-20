<?php
namespace Core\Plugins\Image\Interfaces;

interface IImageSize
{
    public function getHeight(): int;

    public function getWidth(): int;

    public function getFilePrefix(): string;

    public function setHeight(?int $height = null): void;

    public function setWidth(?int $width = null): void;
}
