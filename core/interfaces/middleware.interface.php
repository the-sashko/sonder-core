<?php

namespace Sonder\Core\Interfaces;

use Sonder\Core\ResponseObject;

interface IMiddleware
{
    /**
     * @return void
     */
    public function run(): void;

    /**
     * @return ResponseObject|null
     */
    public function getResponse(): ?ResponseObject;
}
