<?php
/**
 * Basic Class For Model Object Classes
 */
class ModelObjectCore
{
    const DB_DEFAULT_TTL = 30;

    private $_db = null;

    public $scope = null;

    public function __construct(?array $configData = null)
    {
        $this->_db = new DataBasePlugin();
        $this->_db->initDB($configData);
    }

    public function getOne(
        ?string $sql = null,
        ?int    $ttl = null
    ): ?array
    {
        $row = $this->selectRow($sql, $ttl);

        if (empty($row) || !is_array($row)) {
            return null;
        }

        return array_shift($row);
    }

    public function getRow(
        ?string $sql = null,
        ?int    $ttl = null
    ): ?array
    {
        $rows = $this->selectRows($sql, $ttl);

        if (empty($rows) || !is_array($rows)) {
            return null;
        }

        return array_shift($rows);
    }

    public function getRows(
        ?string $sql = null,
        ?int    $ttl = null
    ): ?array
    {
        if (empty($sql)) {
            return null;
        }

        $rows = $this->_db->select($sql, $this->scope, $ttl);

        if (empty($rows) || !is_array($rows)) {
            return null;
        }

        return $rows;
    }

    public function query(?string $sql = null): bool
    {
        if (empty($sql)) {
            return false;
        }

        return $this->_db->select($sql, $this->scope);
    }

    public function start(): bool
    {
        return $this->_db->select();
    }

    public function commit(): bool
    {
        return $this->_db->select();
    }

    public function rollback(): bool
    {
        return $this->_db->select();
    }
}
