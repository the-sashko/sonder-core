<?php
namespace Core\Plugins\Database\Classes;

use Core\Plugins\Database\Interfaces\IDataBaseCache;

use Core\Plugins\Database\Exceptions\DatabaseCacheException;

/**
 * Class For Basic Data Base Cache Functions: Read, Write Or Remove Cache
 */
class DatabaseCache implements IDataBaseCache
{
    const DEFAULT_SCOPE = 'default';

    const DEFAULT_TTL = 60 * 15;

    const ADAPTER_FILE = 'file';
    const ADAPTER_MOCK = 'mock';

    /**
     * @var object|null Instance OF Data Base Cache Adapter
     */
    private $_adapter = null;

    public function __construct(?string $adapter = null)
    {
        if (empty($adapter)) {
            $adapter = static::ADAPTER_FILE;
        }

        switch ($adapter) {
            case static::ADAPTER_MOCK:
                $this->_adapter = null;
                break;

            case static::ADAPTER_FILE:
                $this->_adapter = new DatabaseFileCacheAdapter();
                break;

            default:
                throw new DatabaseCacheException(
                    DatabaseCacheException::MESSAGE_CACHE_ADAPER_NOT_ALLOWED,
                    DatabaseCacheException::CODE_CACHE_ADAPER_NOT_ALLOWED
                );
                break;
        }
    }

    /**
     * Save Cached Data To Adapter
     *
     * @param string|null $sql   SQL Query
     * @param array|null  $data  Data Base Data
     * @param string|null $scope Scope Of Data Base Request
     * @param int|null    $ttl   Time To Live Of Cache
     *
     * @return bool Is Successfully Saved Cached Data
     */
    public function set(
        ?string $sql   = null,
        ?array  $data  = null,
        ?string $scope = null,
        ?int    $ttl   = null
    ): bool
    {
        if (empty($sql)) {
            throw new DatabaseCacheException(
                DatabaseCacheException::MESSAGE_CACHE_SQL_IS_EMPTY,
                DatabaseCacheException::CODE_CACHE_SQL_IS_EMPTY
            );
        }

        if (empty($this->_adapter)) {
            return true;
        }

        if (empty($data)) {
            return false;
        }

        if (empty($ttl)) {
            $ttl = static::DEFAULT_TTL;
        }

        if (empty($scope)) {
            $scope = static::DEFAULT_SCOPE;
        }

        return $this->_adapter->set($sql, $data, $scope, $ttl);
    }

    /**
     * Get Cached Data From Provider
     *
     * @param string|null $sql   SQL Query
     * @param string|null $scope Scope Of Data Base Request
     *
     * @return array|null Cached Data
     */
    public function get(?string $sql = null, ?string $scope = null): ?array
    {
        if (empty($sql)) {
            throw new DatabaseCacheException(
                DatabaseCacheException::MESSAGE_CACHE_SQL_IS_EMPTY,
                DatabaseCacheException::CODE_CACHE_SQL_IS_EMPTY
            );
        }

        if (empty($this->_adapter)) {
            return null;
        }

        if (empty($ttl)) {
            $ttl = static::DEFAULT_TTL;
        }

        if (empty($scope)) {
            $scope = static::DEFAULT_SCOPE;
        }

        $data = $this->_adapter->get($sql, $scope);

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    /**
     * Remove From Adapter All Cached Data Of Data Base Request Scope
     *
     * @param ?string $scope Scope Of Data Base Request
     *
     * @return bool Is Successfully Removed Cached Data
     */
    public function clean(?string $scope = null): bool
    {
        if (empty($this->_adapter)) {
            return true;
        }

        if (empty($scope)) {
            $scope = static::DEFAULT_SCOPE;
        }

        return $this->_adapter->clean($scope);
    }
}
