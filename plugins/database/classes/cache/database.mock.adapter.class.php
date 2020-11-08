<?php
namespace Core\Plugins\Database\Classes;

use Core\Plugins\Database\Interfaces\IDataBaseCacheAdapter;

/**
 * Mock For Data Base Cahe Provider
 */
class DatabaseMockCacheAdapter implements IDataBaseCacheAdapter
{
    /**
     * Mock Of Saving Data To Cache
     *
     * @return bool Is Successfully Saved Cached Data (Always Returns true)
     */
    public function set(): bool
    {
        return true;
    }

    /**
     * Mock Of Get Data From Cache
     *
     * @return array|null Cached Data (Always Returns Empty Array)
     */
    public function get(): ?array
    {
        return null;
    }

    /**
     * Mock Of Removing All Cached Data Of Data Base Request Scope
     *
     * @return bool Is Successfully Removed Cached Data (Always Returns true)
     */
    public function clean(): bool
    {
        return true;
    }
}
