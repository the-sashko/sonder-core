<?php

namespace Sonder\Core;

use Sonder\Enums\CacheTypesEnum;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\CacheException;
use Sonder\Interfaces\ICacheObject;
use Sonder\Interfaces\ICacheTypesEnum;

#[ICacheObject]
final class CacheObject implements ICacheObject
{
    private const DEFAULT_PROTECTED_DIR_PATH = __DIR__ . '/../..';

    /**
     * @var string
     */
    private string $_directoryPath;

    /**
     * @param ICacheTypesEnum $_type
     */
    final public function __construct(
        #[ICacheTypesEnum]
        private ICacheTypesEnum $_type = CacheTypesEnum::DEFAULT
    ) {
        $this->_setDirectoryPath();
    }

    /**
     * @param string $name
     * @return array|null
     */
    final public function get(string $name): ?array
    {
        if (!APP_CACHE) {
            return null;
        }

        $filePath = $this->_getFilePath($name);

        if (!file_exists($filePath) || !is_file($filePath)) {
            return null;
        }

        $values = (string)file_get_contents($filePath);
        $values = (array)json_decode($values, true);

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
     * @return void
     * @throws CacheException
     */
    final public function save(
        string $name,
        ?array $values = null,
        ?int $ttl = null
    ): void {
        if (!APP_CACHE) {
            return;
        }

        $filePath = $this->_getFilePath($name);

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }

        $ttl = empty($ttl) ? APP_CACHE_TTL : $ttl;

        $values = [
            'timestamp' => time() + $ttl,
            'values' => (array)$values,
        ];

        $values = json_encode($values);

        file_put_contents($filePath, $values);

        if (file_exists($filePath) && is_file($filePath)) {
            return;
        }

        throw new CacheException(
            CacheException::MESSAGE_CACHE_CAN_NOT_SAVE_VALUES,
            AppException::CODE_CACHE_CAN_NOT_SAVE_VALUES
        );
    }

    /**
     * @param string $name
     * @return void
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
     * @return void
     */
    final public function removeAll(?string $type = null): void
    {
        $currentType = $this->_type;

        $this->_type = empty($type) ? $currentType : $type;

        $this->_setDirectoryPath();

        $cacheFilePathPattern = sprintf('%s/*.json', $this->_directoryPath);

        foreach ((array)glob($cacheFilePathPattern) as $cacheFilePath) {
            if (is_file($cacheFilePath)) {
                unlink($cacheFilePath);
            }
        }

        $this->_type = $currentType;

        $this->_setDirectoryPath();
    }

    /**
     * @return void
     */
    private function _setDirectoryPath(): void
    {
        $protectedDirPath = CacheObject::DEFAULT_PROTECTED_DIR_PATH;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $protectedDirPath = APP_PROTECTED_DIR_PATH;
        }

        $directoryPath = sprintf(
            '%s/cache/%s',
            $protectedDirPath,
            $this->_type->value
        );

        if (!file_exists($directoryPath) || !is_dir($directoryPath)) {
            mkdir($directoryPath, 755, true);
        }

        $this->_directoryPath = $directoryPath;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function _getFilePath(string $fileName): string
    {
        return sprintf('%s/%s.json', $this->_directoryPath, $fileName);
    }

    /**
     * @param array $values
     * @return bool
     */
    private function _validate(array $values): bool
    {
        return (
            array_key_exists('timestamp', $values) &&
            array_key_exists('values', $values) &&
            is_array($values['values']) &&
            $values['timestamp'] >= time()
        );
    }
}
