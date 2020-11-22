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
        $this->_db->connect($configData);
    }

    public function start(): bool
    {
        return $this->_db->transactionStart();
    }

    public function commit(): bool
    {
        return $this->_db->transactionCommit();
    }

    public function rollback(): bool
    {
        return $this->_db->transactionRollback();
    }

    protected function getOne(?string $sql = null, ?int $ttl = null): ?string
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

        return (string) $value;
    }

    protected function getRow(?string $sql = null, ?int $ttl = null): ?array
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

    protected function getRows(?string $sql = null, ?int $ttl = null): ?array
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

    protected function addRow(
        ?string $table = null,
        ?array  $row  = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($row)) {
            return false;
        }

        $columns = array_keys($row);
        $columns = implode('","', $columns);
        $values  = (string) $this->_getInsertValues($row);

        $sql = '
            INSERT INTO "%s" (
                "%s"
            ) VALUES (
                %s
            );
        ';

        $sql = sprintf($sql, $table, $columns, $values);

        return $this->_db->query($sql, $this->scope);
    }

    protected function updateRows(
        ?string $table     = null,
        ?array  $row       = null,
        ?string $condition = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($row)) {
            return false;
        }

        if (empty($condition)) {
            return false;
        }

        $values = $this->_getUpdateValues($row);

        if (empty($values)) {
            return false;
        }

        $sql = '
            UPDATE "%s"
            SET %s
            WHERE %s;
        ';

        $sql = sprintf($sql, $table, $values, $condition);

        return $this->_db->query($sql, $this->scope);
    }

    protected function updateRowByID(
        ?string $table = null,
        ?array  $row   = null,
        ?int    $idRow = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($row)) {
            return false;
        }

        if (empty($idRow)) {
            return false;
        }

        $condition = sprintf('id = %d', $idRow);

        return $this->updateRows($table, $row, $condition);
    }

    protected function deleteRows(
        ?string $table     = null,
        ?string $condition = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($condition)) {
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

    protected function deteleRowByID(
        ?string $table = null,
        ?int    $idRow = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($idRow)) {
            return false;
        }

        $condition = sprintf('id = %d', $idRow);

        return $this->deleteRows($table, $condition);
    }

    private function _getInsertValues(?array $row = null): ?string
    {
        if (empty($row)) {
            return null;
        }

        foreach ($row as $key => $value) {
            $row[$key] = $this->_getValueString($value);
        }

        return implode(',', $row);
    }

    private function _getUpdateValues(?array $row = null): ?string
    {
        if (empty($row)) {
            return null;
        }

        foreach ($row as $key => $value) {
            $value = $this->_getValueString($value);
            $row[$key] = sprintf('"%s" = %s', (string) $key, (string) $value);
        }

        return implode(',', $row);
    }

    private function _getValueString($value = null): ?string
    {
        if (is_array($value)) {
            $value = json_encode($value);

            return sprintf('\'%s\'', $value);
        }

        if (is_numeric($value)) {
            return (string) $value;
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

        $value = (string) $value;

        return sprintf('\'%s\'', $value);
    }
}
