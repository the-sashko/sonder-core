<?php

class QRcode
{
    /**
     * @param string $text
     * @param string $filePath
     * @param string $errorCorrectionLevel
     * @param int $cellSize
     * @param int $cellIndent
     *
     * @return string|null
     */
    final public static function png(
        string $text,
        string $filePath,
        string $errorCorrectionLevel,
        int    $cellSize,
        int    $cellIndent
    ): ?string
    {
        // Mock png Method Of QR Class From Vendor

        return null;
    }
}

class Router
{
    /**
     * @param string|null $url
     */
    final public function routeRedirect(?string $url = null): void
    {
        // Mock routeRedirect Method Of Router Class
    }

    /**
     * @param string|null $url
     *
     * @return string|null
     */
    final public function routeRewrite(?string $url = null): ?string
    {
        // Mock routeRewrite Method Of Router Class
        return $url;
    }
}

