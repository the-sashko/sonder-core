<?php
class QRcode
{
    public static function png(
        string $text,
        string $filePath,
        string $errorCorrectionLevel,
        int    $cellSize,
        int    $cellIndent
    ): ?string
    {
        // Mock QR Class From Vendor
    }
}

class App
{
    public function __construct()
    {
        // Mock __construct method
    }

    public function run(): void
    {
        // Mock run method        
    }

    public function errorHandler(
        int    $errorCode,
        string $errorMessage,
        string $errorFile,
        int    $errorLine
    ): void
    {
        // Mock errorHandler method
    }

    public function exceptionHandler(?Throwable $exception = null): void
    {
        // Mock exceptionHandler method
    }

    protected function isValidControllerAction(
        ?ControllerCore $controller = null,
        ?string         $action     = null
    ): bool
    {
        // Mock isValidControllerAction method
    }

    protected function isControllerExist(?string $controller = null): bool
    {
        // Mock isControllerExist method
    }
}
