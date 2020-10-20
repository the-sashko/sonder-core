<?php
namespace Core\Plugins\Image\Interfaces;

interface IImagePlugin
{
    public function resize(
        ?string $imageFilePath = null,
        ?string $imageDir      = null,
        ?string $imageName     = null,
        ?string $imageFormat   = null,
        ?array  $sizes         = null
    ): void;
}
