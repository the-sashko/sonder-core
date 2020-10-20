<?php
namespace Core\Plugins\Upload\Interfaces;

interface IUploadFile
{
    public function getName(): string;

    public function getExtension(): ?string;

    public function getFilePath(): string;

    public function getSize(): int;

    public function getError(): int;
}
