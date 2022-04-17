<?php

namespace Sonder\Plugins\Upload\Interfaces;

interface IUploadSettings
{
    /**
     * @return string
     */
    public function getUploadsDirPath(): string;

    /**
     * @return array
     */
    public function getExtensions(): array;

    /**
     * @return int
     */
    public function getMaxSize(): int;
}
