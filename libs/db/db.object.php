<?php
/**
 * summary
 */
class DBObjectClass
{
    use DB;

    const DB_DEFAULT_TTL = 60*60*24*30*6;

    public $scope = 'default';
    public $config = [];

    /**
     * summary
     */
    public function initStore() : void
    {
        $configJSON = file_get_contents($this::DB_CONFIG_PATH);
        $this->config = json_decode($configJSON, true);
    }

    /**
     * summary
     */
    public function get(
        string $sql        = '',
        bool   $isMultiple = true,
        int    $ttl        = self::DB_DEFAULT_TTL
    ) : array
    {
        $res = $this->select($sql, $this->scope, $ttl);

        if (!$isMultiple) {
            $res = count($res)>0 ? $res[0] : $res;
        }

        return $res;
    }

    /**
     * summary
     */
    public function insert(
        string $table  = '',
        array  $columns  = [],
        array  $values = []
    ) : bool
    {
        $columns = $this->_prepare=InsertColumns($columns);
        $values = $this->_prepareInsertValues($values);

        $sql = "
            INSERT INTO `{$table}`
            {$columns}
            VALUES {$values}; 
        ";
        return $this->query($sql, $this->scope);
    }

    /**
     * summary
     */
    public function update(
        string $table     = '',
        array  $colums    = [],
        array  $values    = [],
        string $condition = 'false'
    ) : bool
    {
        $updateValues = $this->_prepareUpdateValues($columns, $values);

        $sql = "
            UPDATE `{$table}`
            SET {$updateValues}
            WHERE {$condition}; 
        ";

        return $this->query($sql, $this->scope);
    }

    /**
     * summary
     */
    public function multipleUpdate(
        string $table           = '',
        array  $items           = [],
        string $conditionColumn = '',
        bool   $isTransaction   = false
    ) : bool
    {
        if (!strlen($conditionColumn) > 0) {
            $conditionColumn = 'id';
        }

        $sql = '';

        foreach ($items as $item) {
            $updateValues = $this->_prepareMultipleUpdateValues($item);
            if (isset($item[$conditionColumn])) {
                $conditionValue = $item[$conditionColumn];
                $condition = "`$conditionColumn` = '{$conditionValue}'";
            } else {
                $condition = 'false';
            }
            $sql = "
                {$sql}
                UPDATE `{$table}`
                SET {$updateValues}
                WHERE {$condition}; 
            ";
        }
        if ($isTransaction) {
            return $this->transactionQuery($sql);
        }

        return $this->query($sql, $this->scope);
    }
    
    /**
     * summary
     */
    public function remove(
        string $table     = '',
        string $condition = 'false'
    ) : bool
    {
        $sql = "
            DELETE FROM `{$table}`
            WHERE {$condition};
        ";
        return $this->query($sql, $this->score);
    }

    /**
     * summary
     */
    public function transactionQuery(string $sql = '') : bool
    {
        $this->begin();
        try {
            $res = $this->query($sql, $this->scope);
            $this->commit();
            return $res;
        } catch (Exception $except) {
            $this->rollback();
        }

    }

    /**
     * summary
     */
    public function begin() : bool
    {
        return $this->transactionStart();
    }

    /**
     * summary
     */
    public function commit() : bool
    {
        return $this->transactionCommit();
    }

    /**
     * summary
     */
    public function rollback() : bool
    {
        return $this->transactionRollback();
    }

    /**
     * summary
     */
    private function _prepareInsertValues(array $values) : string
    {
        if(
            count($values) > 0 &&
            is_array($values[0])
        ){
            foreach ($values as $idx => $value) {
                $value = implode("','", $value);
                $value = "'{$value}'";
                $values[$idx] = $value;
            }
            $values = implode("),(", $values);
            $values = "({$values})";
        } else {
            $values = implode("','", $values);
            $values = "('{$values}')";    
        }

        return $values;
    }

    /**
     * summary
     */
    private function _prepareInsertColumns(array $columns) : string
    {
        $columns = implode("`,`", $columns);
        $columns = "(`{$columns}`)";
        return $columns;
    }

    /**
     * summary
     */
    private function _prepareUpdateValues (
        array $columns = [],
        array $values = []
    ) : string
    {
        $updateValues = [];

        if (count($columns) != count($values)) {
            return '';
        }

        foreach ($colums as $idx => $column) {
            $value = $values[$idx];
            $updateValues[] = "`{$column}` = '{$value}'";
        }

        $updateValues = implode(',', $updateValues);

        return $updateValues;
    }

    /**
     * summary
     */
    private function _prepareMultipleUpdateValues (
        array $values = []
    ) : string
    {
        $updateValues = [];

        foreach ($values as $idx => $value) {
            $value = $values[$idx];
            $updateValues[] = "`{$idx}` = '{$value}'";
        }

        $updateValues = implode(',', $updateValues);

        return $updateValues;
    }
}
?>