<?php
class QRcode
{
    public static function png(
        string $text,
        string $filePath,
        string $errorCorrectionLevel,
        int    $cellSize,
        int    $cellIndent
    ): ?string {
        // Mock png Method Of QR Class From Vendor
    }
}

class Router
{
    public function routeRedirect(?string $url = null): void
    {
        // Mock routeRedirect Method Of Router Class
    }

    public function routeRewrite(?string $url = null): ?string
    {
        // Mock routeRewrite Method Of Router Class
        return $url;
    }
}

