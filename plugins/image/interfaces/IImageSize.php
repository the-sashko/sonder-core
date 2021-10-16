<?php

namespace Sonder\Plugins\Image\Interfaces;

interface IImageSize
{
    /**
     * @return int
     */
    public function getHeight(): int;

    /**
     * @return int
     */
    public function getWidth(): int;

    /**
     * @return string
     */
    public function getFilePrefix(): string;

    /**
     * @param int|null $height
     */
    public function setHeight(?int $height = null): void;

    /**
     * @param int|null $width
     */
    public function setWidth(?int $width = null): void;
}
