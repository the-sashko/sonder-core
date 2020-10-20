<?php
namespace Core\Plugins\Upload\Interfaces;

interface IUploadSettings
{
    public function getUploadsDirPath(): string;

    public function getExtensions(): array;

    public function getMaxSize(): int;
}
