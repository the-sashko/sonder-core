<?php

namespace Sonder\Core;

use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IModelStore;
use Sonder\Plugins\Database\Interfaces\IDatabasePlugin;

#[IModelStore]
class ModelStore implements IModelStore
{
    protected const SCOPE = null;

    protected const TTL = null;

    /**
     * @var IDatabasePlugin
     */
    #[IDatabasePlugin]
    private IDatabasePlugin $_db;

    /**
     * @param array|null $configData
     * @throws CoreException
     */
    public function __construct(?array $configData = null)
    {
        /* @var IDatabasePlugin $databasePlugin */
        $databasePlugin = CoreObject::getPlugin('database');

        $this->_db = $databasePlugin;
        $this->_db->connect($configData);
    }

    /**
     * @return bool
     */
    final public function start(): bool
    {
        return $this->_db->transactionStart();
    }

    /**
     * @return bool
     */
    final public function commit(): bool
    {
        return $this->_db->transactionCommit();
    }

    /**
     * @return bool
     */
    final public function rollback(): bool
    {
        return $this->_db->transactionRollback();
    }

    /**
     * @param string|null $sql
     * @param int|null $ttl
     * @return string|null
     */
    final protected function getOne(
        ?string $sql = null,
        ?int $ttl = null
    ): ?string {
        if (empty($sql)) {
            return null;
        }

        if (empty($ttl)) {
            $ttl = static::TTL;
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
     */
    final protected function getRow(
        ?string $sql = null,
        ?int $ttl = null
    ): ?array {
        if (empty($sql)) {
            return null;
        }

        if (empty($ttl)) {
            $ttl = static::TTL;
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
     */
    final protected function getRows(
        ?string $sql = null,
        ?int $ttl = null
    ): ?array {
        if (empty($sql)) {
            return null;
        }

        if (empty($ttl)) {
            $ttl = static::TTL;
        }

        $rows = $this->_db->select($sql, static::SCOPE, $ttl);

        if (empty($rows) || !is_array($rows)) {
            return null;
        }

        return $rows;
    }

    /**
     * @param string|null $table
     * @param array|null $row
     * @return bool
     */
    final protected function addRow(
        ?string $table = null,
        ?array $row = null
    ): bool {
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

        return $this->_db->query($sql, static::SCOPE);
    }

    /**
     * @param string|null $table
     * @param array|null $row
     * @param string|null $condition
     * @return bool
     */
    final protected function updateRows(
        ?string $table = null,
        ?array $row = null,
        ?string $condition = null
    ): bool {
        if (empty($table) || empty($row) || empty($condition)) {
            return false;
        }

        foreach ($row as $key => $value) {
            $value = $this->_getValueString($value);
            $row[$key] = sprintf('"%s" = %s', $key, $value);
        }

        $row = implode(',', $row);

        $sql = '
            UPDATE "%s"
            SET %s
            WHERE %s;
        ';

        $sql = sprintf($sql, $table, $row, $condition);

        return $this->_db->query($sql, static::SCOPE);
    }

    /**
     * @param string|null $table
     * @param array|null $row
     * @param int|null $idRow
     * @return bool
     */
    final protected function updateRowById(
        ?string $table = null,
        ?array $row = null,
        ?int $idRow = null
    ): bool {
        if (empty($idRow)) {
            return false;
        }

        $condition = sprintf('"id" = %d', $idRow);

        return $this->updateRows($table, $row, $condition);
    }

    /**
     * @param string|null $table
     * @param array|null $row
     * @param int|null $reference
     * @return bool
     */
    final protected function updateRowByReference(
        ?string $table = null,
        ?array $row = null,
        ?int $reference = null
    ): bool {
        if (empty($reference)) {
            return false;
        }

        $condition = sprintf('"reference" = \'%s\'', $reference);

        return $this->updateRows($table, $row, $condition);
    }

    /**
     * @param string|null $table
     * @param string|null $condition
     * @return bool
     */
    final protected function deleteRows(
        ?string $table = null,
        ?string $condition = null
    ): bool {
        if (empty($table) || empty($condition)) {
            return false;
        }

        $sql = '
            DELETE
            FROM "%s"
            WHERE %s;
        ';

        $sql = sprintf($sql, $table, $condition);

        return $this->_db->query($sql, static::SCOPE);
    }

    /**
     * @param string|null $table
     * @param int|null $idRow
     * @return bool
     */
    final protected function deleteRowById(
        ?string $table = null,
        ?int $idRow = null
    ): bool {
        if (empty($idRow)) {
            return false;
        }

        $condition = sprintf('"id" = %d', $idRow);

        return $this->deleteRows($table, $condition);
    }

    /**
     * @param string|null $table
     * @param string|null $reference
     * @return bool
     */
    final protected function deleteRowByReference(
        ?string $table = null,
        ?string $reference = null
    ): bool {
        if (empty($reference)) {
            return false;
        }

        $condition = sprintf('"reference" = \'%s\'', $reference);

        return $this->deleteRows($table, $condition);
    }

    /**
     * @param $value
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
