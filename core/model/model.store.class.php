<?php

namespace Sonder\Core;

use Sonder\Core\Interfaces\IModelStore;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabaseCredentialsException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\DataBasePlugin\DataBasePlugin;

class ModelStore implements IModelStore
{
    /**
     * @var DataBasePlugin
     */
    private DataBasePlugin $_db;

    /**
     * @var string|null
     */
    public ?string $scope = null;

    /**
     * @var int|null
     */
    public ?int $ttl = null;

    /**
     * @param array|null $configData
     * @throws DatabaseCacheException
     * @throws DatabaseCredentialsException
     * @throws DatabasePluginException
     */
    public function __construct(?array $configData = null)
    {
        $this->_db = new DataBasePlugin();
        $this->_db->connect($configData);
    }

    /**
     * @return bool
     * @throws DatabasePluginException
     */
    final public function start(): bool
    {
        return $this->_db->transactionStart();
    }

    /**
     * @return bool
     * @throws DatabasePluginException
     */
    final public function commit(): bool
    {
        return $this->_db->transactionCommit();
    }

    /**
     * @return bool
     * @throws DatabasePluginException
     */
    final public function rollback(): bool
    {
        return $this->_db->transactionRollback();
    }

    /**
     * @param string|null $sql
     * @param int|null $ttl
     * @return string|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final protected function getOne(
        ?string $sql = null,
        ?int    $ttl = null
    ): ?string
    {
        if (empty($sql)) {
            return null;
        }

        if (empty($ttl)) {
            $ttl = $this->ttl;
        }

        $row = $this->getRow($sql, $ttl);

        if (empty($row) || !is_array($row)) {
            return null;
        }

        $value = array_shift($row);

        if (empty($value) || !is_scalar($value)) {
            return null;
        }

        return (string)$value;
    }

    /**
     * @param string|null $sql
     * @param int|null $ttl
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final protected function getRow(
        ?string $sql = null,
        ?int    $ttl = null
    ): ?array
    {
        if (empty($sql)) {
            return null;
        }

        if (empty($ttl)) {
            $ttl = $this->ttl;
        }

        $rows = $this->getRows($sql, $ttl);

        if (empty($rows) || !is_array($rows)) {
            return null;
        }

        return array_shift($rows);
    }

    /**
     * @param string|null $sql
     * @param int|null $ttl
     * @return array|null
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final protected function getRows(
        ?string $sql = null,
        ?int    $ttl = null
    ): ?array
    {
        if (empty($sql)) {
            return null;
        }

        if (empty($ttl)) {
            $ttl = $this->ttl;
        }

        $rows = $this->_db->select($sql, $this->scope, $ttl);

        if (empty($rows) || !is_array($rows)) {
            return null;
        }

        return $rows;
    }

    /**
     * @param string|null $table
     * @param array|null $row
     * @return bool
     * @throws DatabasePluginException
     */
    final protected function addRow(
        ?string $table = null,
        ?array  $row = null
    ): bool
    {
        if (empty($table) || empty($row)) {
            return false;
        }

        $columns = array_keys($row);
        $columns = implode('","', $columns);

        foreach ($row as $key => $value) {
            $row[$key] = $this->_getValueString($value);
        }

        $row = implode(',', $row);

        $sql = '
            INSERT INTO "%s" (
                "%s"
            ) VALUES (
                %s
            );
        ';

        $sql = sprintf($sql, $table, $columns, $row);

        return $this->_db->query($sql, $this->scope);
    }

    /**
     * @param string|null $table
     * @param array|null $row
     * @param string|null $condition
     * @return bool
     * @throws DatabasePluginException
     */
    final protected function updateRows(
        ?string $table = null,
        ?array  $row = null,
        ?string $condition = null
    ): bool
    {
        if (empty($table) || empty($row) || empty($condition)) {
            return false;
        }

        foreach ($row as $key => $value) {
            $value = $this->_getValueString($value);
            $row[$key] = sprintf('"%s" = %s', (string)$key, (string)$value);
        }

        $row = implode(',', $row);

        $sql = '
            UPDATE "%s"
            SET %s
            WHERE %s;
        ';

        $sql = sprintf($sql, $table, $row, $condition);

        return $this->_db->query($sql, $this->scope);
    }

    /**
     * @param string|null $table
     * @param array|null $row
     * @param int|null $idRow
     * @return bool
     * @throws DatabasePluginException
     */
    final protected function updateRowById(
        ?string $table = null,
        ?array  $row = null,
        ?int    $idRow = null
    ): bool
    {
        if (empty($idRow)) {
            return false;
        }

        $condition = sprintf('id = %d', $idRow);

        return $this->updateRows($table, $row, $condition);
    }

    /**
     * @param string|null $table
     * @param string|null $condition
     * @return bool
     * @throws DatabasePluginException
     */
    final protected function deleteRows(
        ?string $table = null,
        ?string $condition = null
    ): bool
    {
        if (empty($table) || empty($condition)) {
            return false;
        }

        $sql = '
            DELETE
            FROM "%s"
            WHERE %s;
        ';

        $sql = sprintf($sql, $table, $condition);

        return $this->_db->query($sql, $this->scope);
    }

    /**
     * @param string|null $table
     * @param int|null $idRow
     * @return bool
     * @throws DatabasePluginException
     */
    final protected function deleteRowById(
        ?string $table = null,
        ?int    $idRow = null
    ): bool
    {
        if (empty($idRow)) {
            return false;
        }

        $condition = sprintf('id = %d', $idRow);

        return $this->deleteRows($table, $condition);
    }

    /**
     * @param null $value
     * @return string|null
     */
    private function _getValueString($value = null): ?string
    {
        if (is_null($value)) {
            return 'NULL';
        }

        if (is_array($value)) {
            $value = json_encode($value);

            return sprintf('\'%s\'', $value);
        }

        if (is_numeric($value)) {
            return (string)$value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value)) {
            return sprintf('\'%s\'', $value);
        }

        if (!is_scalar($value)) {
            $value = json_encode($value);

            return sprintf('\'%s\'', $value);
        }

        $value = (string)$value;

        return sprintf('\'%s\'', $value);
    }
}
