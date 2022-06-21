<?php

namespace Sonder\Plugins\Error;

interface IErrorPlugin
{
    public function displayError(
        int $code,
        string $message,
        string $file,
        int $line,
        ?array $debugBacktrace,
        int $httpResponseCode
    ): bool;
}
