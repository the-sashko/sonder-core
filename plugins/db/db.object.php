<?php
/**
 * Class For Basic Data Basic CRUD Actions And Transactions
 */
class DBObjectClass extends DB
{
    const DB_CONFIG_PATH = __DIR__.'/../../../config/db.json';
    const DB_DEFAULT_LIMIT = 10000;
    const DB_DEFAULT_TTL = 60*60*24*30*6;

    public $scope = 'default';


    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function initStore() : void
    {
        $config = file_get_contents($this::DB_CONFIG_PATH);
        $config = json_decode($config, true);
        parent::initDB($config);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function insert(
        string $table  = '',
        array  $columns  = [],
        array  $values = []
    ) : bool
    {
        $columns = $this->_prepareInsertColumns($columns);
        $values = $this->_prepareInsertValues($values);

        $sql = "
            INSERT INTO \"{$table}\"
            {$columns}
            VALUES {$values};
        ";
        return $this->query($sql, $this->scope);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function update(
        string $table     = '',
        array  $columns   = [],
        array  $values    = [],
        string $condition = 'false'
    ) : bool
    {
        $updateValues = $this->_prepareUpdateValues($columns, $values);

        $sql = "
            UPDATE \"{$table}\"
            SET {$updateValues}
            WHERE {$condition};
        ";

        return $this->query($sql, $this->scope);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function updateByID(
        string $table     = '',
        array  $columns   = [],
        array  $values    = [],
        int    $id        = -1
    ) : bool
    {
        $updateValues = $this->_prepareUpdateValues($columns, $values);
        $condition = "\"id\" = {$id}";

        $sql = "
            UPDATE \"{$table}\"
            SET {$updateValues}
            WHERE {$condition};
        ";

        return $this->query($sql, $this->scope);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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
                $condition = "\"$conditionColumn\" = '{$conditionValue}'";
            } else {
                $condition = 'false';
            }
            $sql = "
                {$sql}
                UPDATE \"{$table}\"
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function remove(
        string $table     = '',
        string $condition = 'false'
    ) : bool
    {
        $sql = "
            DELETE FROM \"{$table}\"
            WHERE {$condition};
        ";

        return $this->query($sql, $this->scope);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function begin() : bool
    {
        return $this->transactionStart();
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function commit() : bool
    {
        return $this->transactionCommit();
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function rollback() : bool
    {
        return $this->transactionRollback();
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _prepareInsertValues(array $values) : string
    {
        if (
            count($values) > 0 &&
            is_array($values[0])
        ) {
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
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _prepareInsertColumns(array $columns) : string
    {
        $columns = implode("\",\"", $columns);
        $columns = "(\"{$columns}\")";

        return $columns;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
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

        foreach ($columns as $idx => $column) {
            $value = $values[$idx];
            $updateValues[] = "\"{$column}\" = '{$value}'";
        }

        $updateValues = implode(',', $updateValues);

        return $updateValues;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _prepareMultipleUpdateValues (
        array $values = []
    ) : string
    {
        $updateValues = [];

        foreach ($values as $idx => $value) {
            $value = $values[$idx];
            $updateValues[] = "\"{$idx}\" = '{$value}'";
        }

        $updateValues = implode(',', $updateValues);

        return $updateValues;
    }
}
?>