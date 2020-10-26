<?php
namespace Core\Plugins\Upload\Interfaces;

interface IUploadPlugin
{
    public function upload(
        ?array  $extensions = null,
        ?int    $maxSize    = null,
        ?string $uploadsDir = null
    ): void;

    public function getFiles(): ?array;

    public function getError(): ?string;
}
