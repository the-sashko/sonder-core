<?php

namespace Sonder\Core;

use Exception;

final class CacheObject
{
    const DEFAULT_TYPE = 'common';

    const DEFAULT_PROTECTED_DIR_PATH = __DIR__ . '/../..';

    const DEFAULT_CACHE_TTL = 60 * 30;

    /**
     * @var bool
     */
    private bool $_enable;

    /**
     * @var string
     */
    private string $_type;

    /**
     * @var string
     */
    private string $_dirPath;

    /**
     * @param string|null $type
     *
     * @param bool $enable
     */
    final public function __construct(
        ?string $type = null,
        bool    $enable = false
    )
    {
        $this->_type = empty($type) ? self::DEFAULT_TYPE : $type;

        $this->_setDirPath();

        $this->_enable = $enable;

        if (APP_MODE == 'prod') {
            $this->_enable = true;
        }

        if (defined('APP_API_MODE') && APP_API_MODE == 'prod') {
            $this->_enable = true;
        }
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    final public function get(string $name): ?array
    {
        if (!$this->_enable) {
            return null;
        }

        $filePath = $this->_getFilePath($name);

        if (!file_exists($filePath) || !is_file($filePath)) {
            return null;
        }

        $values = (string)file_get_contents($filePath);
        $values = (array)json_decode($values);

        if (!$this->_validate($values)) {
            unlink($filePath);

            return null;
        }

        return empty($values['values']) ? null : $values['values'];
    }

    /**
     * @param string $name
     * @param array|null $values
     * @param int|null $ttl
     *
     * @return bool
     *
     * @throws Exception
     */
    final public function set(
        string $name,
        ?array $values = null,
        ?int   $ttl = null
    ): bool
    {
        if (!$this->_enable) {
            return false;
        }

        $filePath = $this->_getFilePath($name);

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }

        $ttl = empty($ttl) ? CacheObject::DEFAULT_CACHE_TTL : $ttl;

        $values = [
            'timestamp' => time() + $ttl,
            'values' => (array)$values,
        ];

        $values = json_encode($values);

        file_put_contents($filePath, $values);

        if (!file_exists($filePath) || !is_file($filePath)) {
            throw new Exception('Can Not Save Cache Values');
        }

        return true;
    }

    /**
     * @param string $name
     */
    final public function remove(string $name): void
    {
        $filePath = $this->_getFilePath($name);

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * @param string|null $type
     */
    final public function removeAll(?string $type = null): void
    {
        $currentType = $this->_type;

        $this->_type = empty($type) ? $currentType : $type;

        $this->_setDirPath();

        $cacheFilePathPattern = sprintf('%s/*.json', $this->_dirPath);

        foreach ((array)glob($cacheFilePathPattern) as $cacheFilePath) {
            if (is_file($cacheFilePath)) {
                unlink($cacheFilePath);
            }
        }

        $this->_type = $currentType;

        $this->_setDirPath();
    }

    private function _setDirPath(): void
    {
        $protectedDirPath = CacheObject::DEFAULT_PROTECTED_DIR_PATH;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $protectedDirPath = APP_PROTECTED_DIR_PATH;
        }

        $dirPath = sprintf(
            '%s/cache/%s',
            $protectedDirPath,
            $this->_type
        );

        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            mkdir($dirPath, 755, true);
        }

        $this->_dirPath = $dirPath;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function _getFilePath(string $fileName): string
    {
        return sprintf('%s/%s.json', $this->_dirPath, $fileName);
    }

    /**
     * @param array $values
     *
     * @return bool
     */
    private function _validate(array $values): bool
    {
        if (
            !array_key_exists('timestamp', $values) ||
            !array_key_exists('values', $values) ||
            !is_array($values['values']) ||
            $values['timestamp'] < time()
        ) {
            return false;
        }

        return true;
    }
}
