<?php
/**
 * Basic Class For Model Object Classes
 */
class ModelObjectCore extends DBObjectClass
{
    /**
     * @var int Count Items On Page
     */
    public $itemsOnPage = 10;

    /**
     * @var string Default Table In Data Base
     */
    public $defaultTableName = null;

    /**
     * summary
     *
     * @return string Get Default Table In Data Base
     */
    public function getDefaultTableName(): string
    {
        if (empty($defaultTableName)) {
            throw new Exception('Default DB Is Missing Or Has Bad Format');
        }

        return $this->defaultTableName;
    }

    /**
     * Get One Item By SQL Query
     *
     * @param string|null $sql SQL SELECT Query
     * @param int         $ttl Time To Live Data Base Cache
     *
     * @return array Item Data
     */
    public function getOne(
        ?string $sql = null,
        int     $ttl = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($sql)) {
            return null;
        }

        return $this->get($sql, false, $ttl);
    }

    /**
     * Get List Of Items From Data Base By Condition
     *
     * @param string|null $table     Data Base Table
     * @param array|null  $columns   List Of Returned Data Base Table Columns
     * @param array       $condition Data Base Selection Condition
     * @param string      $limit     Limit And Offset Of Items Selection
     * @param string|null $orderBy   SQL Sorting Condition
     * @param int         $ttl       Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getByCondition(
        ?string $table     = null,
        ?array  $columns   = null,
        string  $condition = 'true',
        ?array  $limit     = null,
        ?string $orderBy   = null,
        int     $ttl       = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($table)) {
            return null;
        }

        $columns = $this->_prepareSelectedColumns($columns);

        $queryLimit = $this->_prepareQueryLimit($limit);

        if (empty($orderBy)) {
            $orderBy = '"id" DESC';
        }

        $sql = "
            SELECT
                {$columns}    
            FROM \"{$table}\"
            WHERE {$condition}
            ORDER BY {$orderBy}
            {$queryLimit};
        ";

        return $this->get($sql, true, $ttl);
    }

    /**
     * Get One Items From Data Base By Condition
     *
     * @param string|null $table     Data Base Table
     * @param array|null  $columns   List Of Returned Data Base Table Columns
     * @param array       $condition Data Base Selection Condition
     * @param int         $ttl       Time To Live Data Base Cache
     *
     * @return array Item Data
     */
    public function getOneByCondition(
        ?string $table     = null,
        ?array  $columns   = null,
        string  $condition = 'true',
        int     $ttl       = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($table)) {
            return null;
        }

        $columns = $this->_prepareSelectedColumns($columns);

        $sql = "
            SELECT
                {$columns}    
            FROM \"{$table}\"
            WHERE {$condition}
            OFFSET 0
            LIMIT 1;
        ";

        return $this->get($sql, false, $ttl);
    }

    /**
     * Get All Items From Data Base By Condition
     *
     * @param string|null $table     Data Base Table
     * @param array|null  $columns   List Of Returned Data Base Table Columns
     * @param array       $condition Data Base Selection Condition
     * @param string|null $orderBy   SQL Sorting Condition
     * @param int         $ttl       Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getAllByCondition(
        ?string $table     = null,
        ?array  $columns   = null,
        string  $condition = 'true',
        ?string $orderBy   = null,
        int     $ttl       = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($table)) {
            return null;
        }

        return $this->getByCondition(
            $table,
            $columns,
            $condition,
            null,
            $orderBy,
            $ttl
        );
    }

    /**
     * Get All Items From Data Base Table
     *
     * @param string|null $table   Data Base Table
     * @param array|null  $columns List Of Returned Data Base Table Columns
     * @param string|null $orderBy SQL Sorting Condition
     * @param int         $ttl     Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getAll(
        ?string $table   = null,
        ?array  $columns = null,
        ?string $orderBy = null,
        int     $ttl     = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($table)) {
            return null;
        }

        return $this->getAllByCondition(
            $table,
            $columns,
            'true',
            $orderBy,
            $ttl
        );
    }

    /**
     * Get Items From Data Base By Condition And Page
     *
     * @param string|null $table     Data Base Table
     * @param array|null  $columns   List Of Returned Data Base Table Columns
     * @param array       $condition Data Base Selection Condition
     * @param int         $page      Page Number
     * @param string|null $orderBy   SQL Sorting Condition
     * @param int         $ttl       Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getByPageWithCondition(
        ?string $table     = null,
        ?array  $columns   = null,
        string  $condition = 'true',
        int     $page      = 1,
        ?string $orderBy   = null,
        int     $ttl       = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($table)) {
            return null;
        }

        $limit = $this->getQueryLimitByPage($page);

        return $this->getByCondition(
            $table,
            $columns,
            $condition,
            $limit,
            $orderBy,
            $ttl
        );
    }

    /**
     * Get All Items From Data Base By Page
     *
     * @param string|null $table   Data Base Table
     * @param array|null  $columns List Of Returned Data Base Table Columns
     * @param int         $page    Page Number
     * @param string|null $orderBy SQL Sorting Condition
     * @param int         $ttl     Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getAllByPage(
        ?string $table   = null,
        ?array  $columns = null,
        int     $page    = 1,
        ?string $orderBy = null,
        int     $ttl     = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($table)) {
            return null;
        }

        return $this->getByPageWithCondition(
            $table,
            $columns,
            'true',
            $page,
            $orderBy,
            $ttl
        );
    }

    /**
     * Get One Item From Data Base By ID
     *
     * @param string|null $table Data Base Table
     * @param int         $id    Item ID
     * @param int         $ttl   Time To Live Data Base Cache
     *
     * @return array Item Data
     */
    public function getByID(
        ?string $table = null,
        ?int    $id    = null,
        int     $ttl   = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($table)) {
            return null;
        }

        if (empty($id) || $id < 1) {
            return null;
        }

        $condition = "\"id\" = {$id}";

        return $this->getOneByCondition($table, null, $condition, $ttl);
    }

    /**
     * Get One Item From Data Base By Slug
     *
     * @param string|null $table Data Base Table
     * @param string|null $slug  Item Slug
     * @param int         $ttl   Time To Live Data Base Cache
     *
     * @return array Item Data
     */
    public function getBySlug(
        ?string $table = null,
        ?string $slug  = null,
        int     $ttl   = self::DB_DEFAULT_TTL
    ): ?array
    {
        if (empty($table)) {
            return null;
        }

        if (empty($slug)) {
            return null;
        }

        $condition = "\"slug\" = '{$slug}'";

        return $this->getOneByCondition($table, null, $condition, $ttl);
    }

    /**
     * Remove Item From Data Base By ID
     *
     * @param string|null $table Data Base Table
     * @param int|null     $id    Item ID
     *
     * @return bool Is Item Successfully Removed
     */
    public function removeByID(?string $table = null, ?int $id = null): bool
    {
        if (empty($table)) {
            return false;
        }

        if (empty($id) || (int) $id < 1) {
            return false;
        }

        $condition = "\"id\" = {$id}";

        return $this->remove($table, $condition);
    }

    /**
     * Get Maximum ID In Data Base Table
     *
     * @param string|null $table Data Base Table
     *
     * @return int Maximum ID In Data Base Table
     */
    public function getMaxID(?string $table = nul): int
    {
        if (empty($table)) {
            return 0;
        }

        $sql = "
            SELECT
                MAX(\"id\") AS max_id
            FROM \"{$table}\"
            GROUP BY \"id\";
        ";
        $res = $this->get($sql, FALSE, self::DB_DEFAULT_TTL);

        if (!array_key_exists('max_id', $res)) {
            return 0;
        }

        return (int) $res['max_id'];
    }

    /**
     * Count Items In Data Base Table By Condition
     *
     * @param string|null $table     Data Base Table
     * @param array       $condition Data Base Selection Condition
     * @param int         $ttl       Time To Live Data Base Cache
     *
     * @return int Count Items In Data Base Table
     */
    public function count(
        ?string $table     = null,
        string  $condition = 'true',
        int     $ttl       = self::DB_DEFAULT_TTL
    ): int
    {
        if (empty($table)) {
            return 0;
        }

        $sql = "
            SELECT COUNT(\"id\") AS count
            FROM {$table}
            WHERE {$condition};
        ";

        $res = $this->select($sql, $this->scope, $ttl);

        if (count($res) < 1) {
            return 0;
        }

        $res = $res[0];

        if (!is_array($res) || !array_key_exists('count', $res)) {
            return 0;
        }

        $res = (int) $res['count'];
        $res = $res > 0 ? $res : 0;

        return $res;
    }

    /**
     * Count Items In Data Base Table By Condition
     *
     * @param string|nul $table Data Base Table
     * @param int        $ttl   Time To Live Data Base Cache
     *
     * @return int Count Items In Data Base Table
     */
    public function countAll(
        ?string $table = null,
        int     $ttl   = self::DB_DEFAULT_TTL
    ): int
    {
        return $this->count($table, 'true', $ttl);
    }

    /**
     * Get Offset And Limit Params From Page
     *
     * @param int $page Page Number
     *
     * @return array Offset And Limit Params
     */
    public function getQueryLimitByPage(int $page = 1): array
    {
        if ($page < 1) {
            throw new Exception("Invalid Content Page Value");
        }

        $limit  = $this->itemsOnPage;
        $offset = $this->itemsOnPage * ($page - 1);

        return [$limit, $offset];
    }

    /**
     * Get SQL SELECT List Of Columns From List Of Columns
     *
     * @param  array|null $selected Columns List Of Columns
     *
     * @return string SQL SELECT List Of Columns
     */
    private function _prepareSelectedColumns(?array $columns = null): string
    {
        if (empty($columns)) {
            return '*';
        }

        return implode(',', $columns);
    }

    /**
     * Get SQL SELECT Limit From Limit And Offset Params
     *
     * @param  array|null $limit Limit And Offset Params
     *
     * @return string SQL SELECT Limit
     */
    private function _prepareQueryLimit(?array $limit = null): string
    {
        if (empty($limit)) {
            return '';
        }

        if (2 !== count($limit)) {
            return '';
        }

        $offset = (int) $limit[1];
        $limit  = (int) $limit[0];

        if ($offset < 0) {
            throw new Exception("Invalid SQL OFFSET Value");
        }

        if ($limit < 1) {
            throw new Exception("Invalid SQL LIMIT Value");
        }

        $limitSQL  = "LIMIT {$limit}";
        $offsetSQL = "OFFSET {$offset}";

        return "{$limitSQL}\n{$offsetSQL}";
    }
}
