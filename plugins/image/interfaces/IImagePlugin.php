<?php

namespace Sonder\Plugins\Image\Interfaces;

interface IImagePlugin
{
    /**
     * @param string|null $imageFilePath
     * @param string|null $imageDirPath
     * @param string|null $imageFileName
     * @param string|null $imageFormat
     * @param array|null $sizes
     */
    public function resize(
        ?string $imageFilePath = null,
        ?string $imageDirPath = null,
        ?string $imageFileName = null,
        ?string $imageFormat = null,
        ?array  $sizes = null
    ): void;
}
