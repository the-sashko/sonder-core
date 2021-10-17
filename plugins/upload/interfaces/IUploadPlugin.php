<?php

namespace Sonder\Plugins\Upload\Interfaces;

interface IUploadPlugin
{
    /**
     * @param array|null $extensions
     * @param int|null $maxSize
     * @param string|null $uploadsDir
     */
    public function upload(
        ?array  $extensions = null,
        ?int    $maxSize = null,
        ?string $uploadsDir = null
    ): void;

    /**
     * @return array|null
     */
    public function getFiles(): ?array;

    /**
     * @return string|null
     */
    public function getError(): ?string;
}
