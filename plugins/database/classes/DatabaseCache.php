<?php

namespace Sonder\Plugins\Database\Classes;

use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabaseException;
use Sonder\Plugins\Database\Interfaces\IDataBaseCache;

final class DatabaseCache implements IDataBaseCache
{
    const DEFAULT_SCOPE = 'default';

    const DEFAULT_TTL = 60 * 15;

    const ADAPTER_FILE = 'file';
    const ADAPTER_MOCK = 'mock';

    /**
     * @var DatabaseFileCacheAdapter|null
     */
    private ?DatabaseFileCacheAdapter $_adapter = null;

    /**
     * @param string|null $adapter
     *
     * @throws DatabaseCacheException
     */
    final public function __construct(?string $adapter = null)
    {
        if (empty($adapter)) {
            $adapter = DatabaseCache::ADAPTER_FILE;
        }

        $this->_adapter = match ($adapter) {
            DatabaseCache::ADAPTER_MOCK => null,
            DatabaseCache::ADAPTER_FILE => new DatabaseFileCacheAdapter(),
            default => throw new DatabaseCacheException(
                DatabaseCacheException::MESSAGE_CACHE_ADAPTER_NOT_ALLOWED,
                DatabaseException::CODE_CACHE_ADAPTER_NOT_ALLOWED
            ),
        };
    }

    /**
     * @param string|null $sql
     * @param array|null $data
     * @param string|null $scope
     * @param int|null $ttl
     *
     * @return bool
     *
     * @throws DatabaseCacheException
     */
    final public function set(
        ?string $sql = null,
        ?array  $data = null,
        ?string $scope = null,
        ?int    $ttl = null
    ): bool
    {
        if (empty($sql)) {
            throw new DatabaseCacheException(
                DatabaseCacheException::MESSAGE_CACHE_SQL_IS_EMPTY,
                DatabaseException::CODE_CACHE_SQL_IS_EMPTY
            );
        }

        if (empty($this->_adapter)) {
            return true;
        }

        if (empty($data)) {
            return false;
        }

        if (empty($ttl)) {
            $ttl = DatabaseCache::DEFAULT_TTL;
        }

        if (empty($scope)) {
            $scope = DatabaseCache::DEFAULT_SCOPE;
        }

        return $this->_adapter->set($sql, $data, $scope, $ttl);
    }

    /**
     * @param string|null $sql
     * @param string|null $scope
     *
     * @return array|null
     *
     * @throws DatabaseCacheException
     */
    final public function get(
        ?string $sql = null,
        ?string $scope = null
    ): ?array
    {
        if (empty($sql)) {
            throw new DatabaseCacheException(
                DatabaseCacheException::MESSAGE_CACHE_SQL_IS_EMPTY,
                DatabaseException::CODE_CACHE_SQL_IS_EMPTY
            );
        }

        if (empty($this->_adapter)) {
            return null;
        }

        if (empty($scope)) {
            $scope = DatabaseCache::DEFAULT_SCOPE;
        }

        $data = $this->_adapter->get($sql, $scope);

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    /**
     * @param string|null $scope
     *
     * @return bool
     */
    final public function clean(?string $scope = null): bool
    {
        if (empty($this->_adapter)) {
            return true;
        }

        if (empty($scope)) {
            $scope = DatabaseCache::DEFAULT_SCOPE;
        }

        return $this->_adapter->clean($scope);
    }
}
