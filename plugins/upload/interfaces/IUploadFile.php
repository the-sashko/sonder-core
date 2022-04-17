<?php

namespace Sonder\Plugins\Upload\Interfaces;

interface IUploadFile
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string|null
     */
    public function getExtension(): ?string;

    /**
     * @return string
     */
    public function getFilePath(): string;

    /**
     * @return int
     */
    public function getSize(): int;

    /**
     * @return int
     */
    public function getError(): int;
}
