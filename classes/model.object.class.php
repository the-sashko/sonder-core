<?php
/**
 * Basic Class For Model Object Classes
 */
class ModelObjectCore
{
    private $_db = null;

    public $scope = null;

    public $ttl = null;

    public function __construct(?array $configData = null)
    {
        $this->_db = new DataBasePlugin();
        $this->_db->initDB($configData);
    }

    protected function getOne(?string $sql = null, ?int $ttl = null): ?array
    {
        if (empty($sql)) {
            return null;
        }

        if (empty($ttl)) {
            $tll = $this->ttl;
        }

        $row = $this->selectRow($sql, $ttl);

        if (empty($row) || !is_array($row)) {
            return null;
        }

        return array_shift($row);
    }

    protected function getRow(?string $sql = null, ?int $ttl = null): ?array
    {
        if (empty($sql)) {
            return null;
        }

        if (empty($ttl)) {
            $tll = $this->ttl;
        }

        $rows = $this->selectRows($sql, $ttl);

        if (empty($rows) || !is_array($rows)) {
            return null;
        }

        return array_shift($rows);
    }

    protected function getRows(?string $sql = null, ?int $ttl = null): ?array
    {
        if (empty($sql)) {
            return null;
        }

        if (empty($ttl)) {
            $tll = $this->ttl;
        }

        $rows = $this->_db->select($sql, $this->scope, $ttl);

        if (empty($rows) || !is_array($rows)) {
            return null;
        }

        return $rows;
    }

    protected function addRows(
        ?string $table = null,
        ?array  $rows  = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($rows)) {
            return false;
        }

        $columns = array_keys($values);
        $columns = implode(', ', $columns);
        $values  = implode(', ', $rows);

        $sql = '
            INSERT INTO %s (
                %s
            ) VALUES (
                %s
            );
        ';

        $sql = sprintf($sql, $table, $columns, $values);

        return $this->_db->query($sql, $this->scope);
    }

    protected function updateRows(
        ?string $table     = null,
        ?array  $rows      = null,
        ?string $condition = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($rows)) {
            return false;
        }

        if (empty($condition)) {
            return false;
        }

        foreach ($rows as $key => $row) {
            $rows[$key] = sprintf('%s = %s', $key, $row);
        }

        $rows = implode(', ', $rows);

        $sql = '
            UPDATE %s
            SET %s
            WHERE %s;
        ';

        $sql = sprintf($sql, static::CRON_TABLE, $rows, $condition);

        return $this->query($sql, $this->scope);
    }

    protected function updateRowByID(
        ?string $table = null,
        ?array  $row   = null,
        ?int    $id    = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($row)) {
            return false;
        }

        if (empty($id)) {
            return false;
        }

        $condition = sprintf('id = %d', $id)

        return $this->updateRows($table, $row, $condition);
    }

    protected function deteleRows(
        ?string $table    = null,
        ?string $codition = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($condition)) {
            return false;
        }

        $sql = '
            DELETE FROM %s
            WHERE %s;
        ';

        $sql = sprintf($sql, $table, $condition);

        return $this->query($sql, $this->scope);
    }

    protected function deteleRowByID(
        ?string $table = null,
        ?id     $id    = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($id)) {
            return false;
        }

        $condition = sprintf('id = %d', $id);

        return $this->deleteRows($table, $condition);
    }

    protected function start(): bool
    {
        return $this->_db->select();
    }

    protected function commit(): bool
    {
        return $this->_db->select();
    }

    protected function rollback(): bool
    {
        return $this->_db->select();
    }
}
