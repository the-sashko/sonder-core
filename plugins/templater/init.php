<?php

require_once __DIR__ . '/../language/init.php';
require_once __DIR__ . '/TemplaterPlugin.php';
require_once __DIR__ . '/functions.php';

use function Sonder\renderPage;
use function Sonder\renderPart;

/**
 * @param string|null $page
 *
 * @throws Exception
 */
function __page(?string $page = null):  void
{
    renderPage($page);
}

/**
 * @param string|null $part
 * @param array|null $values
 * @param bool $isCache
 * @param int|null $ttl
 *
 * @throws Exception
 */
function __part(
    ?string $part = null,
    ?array  $values = null,
    bool    $isCache = false,
    ?int    $ttl = null
): void
{
    $ttl = empty($ttl) ? (int)$GLOBALS['template']['ttl'] : $ttl;

    if (!$isCache) {
        $ttl = 0;
    }

    renderPart($part, $values, $ttl);
}
