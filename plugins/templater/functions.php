<?php

namespace Sonder;

use Exception;

/**
 * @param string|null $page
 *
 * @throws Exception
 */
function renderPage(?string $page = null): void
{
    foreach ($GLOBALS['template_params'] as $param => $value) {
        $$param = $value;
    }

    $pageFilePath = sprintf(
        '%s/%s/pages/%s.phtml',
        $GLOBALS['template_dir'],
        $GLOBALS['template_area'],
        $page
    );

    if (!file_exists($pageFilePath) || !is_file($pageFilePath)) {
        throw new Exception(sprintf('Template Page "%s" Missing', $page));
    }

    include_once($pageFilePath);
}

/**
 * @param string|null $part
 * @param array|null $values
 * @param int|null $ttl
 *
 * @return bool
 *
 * @throws Exception
 */
function renderPart(
    ?string $part = null,
    ?array  $values = null,
    ?int    $ttl = null
): bool
{
    if (!empty($ttl)) {
        $cacheData = getTemplateDataFromCache($part);

        if (!empty($cacheData)) {
            echo $cacheData;

            return true;
        }

        ob_start();
    }

    if (!empty($values)) {
        foreach ($values as $templateDataKey => $templateDataValue) {
            $GLOBALS['template_params'][$templateDataKey] = $templateDataValue;
        }
    }

    foreach ($GLOBALS['template_params'] as $param => $value) {
        $$param = $value;
    }

    $templatePartFile = sprintf(
        '%s/%s/parts/%s.phtml',
        $GLOBALS['template_dir'],
        $GLOBALS['template_area'],
        $part
    );

    if (!file_exists($templatePartFile) || !is_file($templatePartFile)) {
        throw new Exception(sprintf('Template Part "%s" Is Not Found', $part));
    }

    include($templatePartFile);

    if ($ttl > 0) {
        $partData = (string)ob_get_clean();
        saveTemplateDataToCache($part, $partData, $ttl);

        echo $partData;
    }

    return true;
}

/**
 * @param string|null $part
 *
 * @return string|null
 */
function getTemplateDataFromCache(?string $part = null): ?string
{
    $cacheFilePath = sprintf(
        '%s/%s.html',
        $GLOBALS['template_cache_dir'],
        $part
    );

    if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
        $partCacheData = file_get_contents($cacheFilePath);
        $partCacheData = json_decode($partCacheData, true);

        if (
            array_key_exists('timestamp', $partCacheData) &&
            time() < (int)$partCacheData['timestamp'] &&
            array_key_exists('data', $partCacheData)
        ) {
            return (string)$partCacheData['data'];
        }
    }

    return null;
}

/**
 * @param string|null $partName
 * @param string|null $partData
 * @param int|null $ttl
 */
function saveTemplateDataToCache(
    ?string $partName = null,
    ?string $partData = null,
    ?int    $ttl = null
): void
{
    $cacheData = [
        'timestamp' => time() + $ttl,
        'data' => $partData
    ];

    $cacheFilePath = sprintf(
        '%s/%s.html',
        $GLOBALS['template_cache_dir'],
        $partName
    );

    if (file_exists($cacheFilePath) && is_file($cacheFilePath)) {
        unlink($cacheFilePath);
    }

    file_put_contents($cacheFilePath, json_encode($cacheData));
    chmod($cacheFilePath, 0775);
}

/**
 * @param string|null $page
 * @throws Exception
 */
function __page(?string $page = null): void
{
    renderPage($page);
}

/**
 * @param string|null $part
 * @param array|null $values
 * @param bool $isCache
 *
 * @throws Exception
 */
function __part(
    ?string $part = null,
    ?array  $values = null,
    bool    $isCache = false
): void
{
    $ttl = $isCache ? (int)$GLOBALS['template_ttl'] : 0;
    renderPart($part, $values, $ttl);
}
