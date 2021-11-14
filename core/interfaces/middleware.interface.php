<?php

namespace Sonder\Core\Interfaces;

use Sonder\Core\ResponseObject;

interface IMiddleware
{
    public function run(): void;

    /**
     * @return ResponseObject|null
     */
    public function getResponse(): ?ResponseObject;
}
