<?php
/**
 * Class For Basic Data Basic CRUD Actions And Transactions
 */
class DBObjectClass extends DB
{
    /**
     * @var string Path To Data Base Config
     */
    const DB_CONFIG_PATH = __DIR__.'/../../../config/db.json';

    /**
     * @var int Default Selected Rows Limit
     */
    const DB_DEFAULT_LIMIT = 10000;

    /**
     * @var int Default Value Of Data Base Cache Time To Live
     */
    const DB_DEFAULT_TTL = 60*60*24*30*6;

    /**
     * @var string Base Cache Time To Live
     */
    public $scope = 'default';

    /**
     * Create Connection To DB
     */
    public function initStore(): void
    {
        $config = file_get_contents($this::DB_CONFIG_PATH);
        $config = json_decode($config, true);
        parent::initDB($config);
    }

    /**
     * Execute SQL SELECT Query And Get Once Or Muliple Rows From Data Base
     *
     * @param ?string $sql        SQL SELECT Query
     * @param bool    $isMultiple Is Getting Multiple Rows From Data Base
     * @param int     $ttl        Time To Live Data Base Cache
     *
     * @return array Rows From Data Base
     */
    public function get(
        ?string $sql       = null,
        bool   $isMultiple = true,
        int    $ttl        = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($sql)) {
            return null;
        }

        $res = $this->select($sql, $this->scope, $ttl);

        if (empty($res)) {
            return null;
        }

        if (!$isMultiple) {
            $res = count($res) > 0 ? $res[0] : $res;
        }

        return $res;
    }

    /**
     * Execute SQL INSERT Query
     *
     * @param ?string $table   Data Base Table
     * @param ?array  $columns Data Base Table Columns
     * @param ?array  $values  Inserted Values
     *
     * @return bool Is Data Successfully Inserted
     */
    public function insert(
        ?string $table   = null,
        ?array  $columns = null,
        ?array  $values  = null
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($columns)) {
            return false;
        }

        if (empty($values)) {
            return false;
        }

        $columns = $this->_prepareInsertColumns($columns);
        $values  = $this->_prepareInsertValues($values);

        $sql = "
            INSERT INTO \"{$table}\"
            {$columns}
            VALUES {$values};
        ";
        return $this->query($sql, $this->scope);
    }

    /**
     * Execute SQL UPDATE Query
     *
     * @param ?string $table     Data Base Table
     * @param ?array  $columns   Data Base Table Columns
     * @param ?array  $values    Update Values
     * @param string  $condition Update Condition
     *
     * @return bool Is Data Successfully Updated
     */
    public function update(
        ?string $table     = null,
        ?array  $columns   = null,
        ?array  $values    = null,
        string  $condition = 'false'
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($columns)) {
            return false;
        }

        if (empty($values)) {
            return false;
        }

        $updateValues = $this->_prepareUpdateValues($columns, $values);

        $sql = "
            UPDATE \"{$table}\"
            SET {$updateValues}
            WHERE {$condition};
        ";

        return $this->query($sql, $this->scope);
    }

    /**
     * Execute SQL UPDATE Query By ID Value
     *
     * @param ?string $table   Data Base Table
     * @param ?array  $columns Data Base Table Columns
     * @param ?array  $values  Update Values
     * @param int     $id      ID Value Condition
     *
     * @return bool Is Data Successfully Updated
     */
    public function updateByID(
        ?string $table   = null,
        ?array  $columns = null,
        ?array  $values  = null,
        int     $id      = -1
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($columns)) {
            return false;
        }

        if (empty($values)) {
            return false;
        }

        if ($id < 1) {
            return false;
        }

        $updateValues = $this->_prepareUpdateValues($columns, $values);
        $condition    = "\"id\" = {$id}";

        $sql = "
            UPDATE \"{$table}\"
            SET {$updateValues}
            WHERE {$condition};
        ";

        return $this->query($sql, $this->scope);
    }

    /**
     * Execute Multiple SQL UPDATE Queries
     *
     * @param string $table           Data Base Table
     * @param array  $items           List Of Entries For Update
     * @param string $conditionColumn Data Base Table Column For Condition
     * @param bool   $isTransaction   Is Use SQL Transaction For UPDATE Queries
     *
     * @return bool Is Data Successfully Updated
     */
    public function multipleUpdate(
        ?string $table           = null,
        ?array  $items           = null,
        ?string $conditionColumn = null,
        bool   $isTransaction   = FALSE
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($items)) {
            return false;
        }

        if (empty($conditionColumn)) {
            $conditionColumn = 'id';
        }

        $sql = '';

        foreach ($items as $item) {
            $updateValues = $this->_prepareMultipleUpdateValues($item);

            $condition = 'FALSE';

            if (isset($item[$conditionColumn])) {
                $conditionValue = $item[$conditionColumn];
                $condition      = "\"$conditionColumn\" = '{$conditionValue}'";
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
     * Execute SQL DELETE Query
     *
     * @param ?string $table     Data Base Table
     * @param string  $condition Delete Condition
     *
     * @return bool Is Data Successfully Removed
     */
    public function remove(
        ?string $table     = null,
        string $condition = 'false'
    ): bool
    {
        if (empty($table)) {
            return false;
        }

        $sql = "
            DELETE FROM \"{$table}\"
            WHERE {$condition};
        ";

        return $this->query($sql, $this->scope);
    }

    /**
     * Execute SQL Query Using Transaction
     *
     * @param string $sql SQL Query
     *
     * @return bool Is SQL Query Successfully Executed
     */
    public function transactionQuery(?string $sql = null): bool
    {
        if (empty($sql)) {
            return false;
        }

        $this->begin();

        try {
            $res = $this->query($sql, $this->scope);
            $this->commit();

            return $res;
        } catch (Exception $except) {
            $this->rollback();
        }

        return false;
    }

    /**
     * Begin SQL Transaction
     *
     * @return bool Is SQL Transaction Successfully Started
     */
    public function begin(): bool
    {
        return $this->transactionStart();
    }

    /**
     * Commit SQL Transaction
     *
     * @return bool Is SQL Transaction Successfully Commited
     */
    public function commit(): bool
    {
        return $this->transactionCommit();
    }

    /**
     * Rollback SQL Transaction
     *
     * @return bool Is SQL Transaction Was Successfully Rollback
     */
    public function rollback(): bool
    {
        return $this->transactionRollback();
    }

    /**
     * Convert List Of Values To SQL INSERT Format
     *
     * @param array $values List Of Values For Insert To Data Base
     *
     * @return string Part Of SQL INSERT Query
     */
    private function _prepareInsertValues(array $values): string
    {
        if (
            count($values) < 1 ||
            !is_array($values[0])
        ) {
            $values = implode("','", $values);
            $values = "('{$values}')";

            return $values;
        }

        foreach ($values as $idx => $value) {
            $value = implode("','", $value);
            $value = "'{$value}'";

            $values[$idx] = $value;
        }

        $values = implode("),(", $values);
        $values = "({$values})";

        return $values;
    }

    /**
     * Convert List Of Data Base Table Colums To SQL INSERT Format
     *
     * @param array $columns List Of Data Base Table Colums
     *
     * @return string Part Of SQL INSERT Query
     */
    private function _prepareInsertColumns(array $columns): string
    {
        $columns = implode("\",\"", $columns);
        $columns = "(\"{$columns}\")";

        return $columns;
    }

    /**
     * Convert List Of Values AND Data Base Table Colums To SQL UPDATE Format
     *
     * @param array $columns List Of Data Base Table Colums
     * @param array $values  List Of Values For Updating In Data Base
     *
     * @return string Part Of SQL UPDATE Query
     */
    private function _prepareUpdateValues(
        array $columns,
        array $values
    ): string
    {
        $updateValues = [];

        if (count($columns) !== count($values)) {
            return '';
        }

        foreach ($columns as $idx => $column) {
            $value          = $values[$idx];
            $updateValues[] = "\"{$column}\" = '{$value}'";
        }

        $updateValues = implode(',', $updateValues);

        return $updateValues;
    }

    /**
     * Convert List Of Values To SQL UPDATE Format With Multiple Updating
     *
     * @param array $values List Of Values For Updateing In Data Base
     *
     * @return string Part Of SQL UPDATE Query
     */
    private function _prepareMultipleUpdateValues(array $values): string
    {
        $updateValues = [];

        foreach ($values as $idx => $value) {
            $value          = $values[$idx];
            $updateValues[] = "\"{$idx}\" = '{$value}'";
        }

        $updateValues = implode(',', $updateValues);

        return $updateValues;
    }
}
