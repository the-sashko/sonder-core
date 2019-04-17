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
    public $defaultTableName = NULL;

    /**
     * summary
     *
     * @return string Get Default Table In Data Base
     */
    public function getDefaultTableName() : string
    {
        $defaultTableName = (string) $this->defaultTableName;

        if (strlen($defaultTableName) < 1) {
            throw new Exception('Default DB Is Missing Or Has Bad Format');
        }

        return $this->defaultTableName;
    }

    /**
     * Get One Item By SQL Query
     *
     * @param string $sql SQL SELECT Query
     * @param int    $ttl Time To Live Data Base Cache
     *
     * @return array Item Data
     */
    public function getOne(
        string $sql = '',
        int    $ttl = self::DB_DEFAULT_TTL
    ) : array
    {
        return $this->get($sql, false, $ttl);
    }

    /**
     * Get List Of Items From Data Base By Condition
     *
     * @param string $table           Data Base Table
     * @param array  $selectedColumns List Of Returned Data Base Table Columns
     * @param array  $condition       Data Base Selection Condition
     * @param string $limit           Limit And Offset Of Items Selection
     * @param int    $ttl             Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getByCondition(
        string $table           = '',
        array  $selectedColumns = [],
        string $condition       = 'true',
        array  $limit           = [],
        int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
        $selectedColumns = $this->_prepareSelectedColumns(
            $selectedColumns
        );
        
        $queryLimit = $this->_prepareQueryLimit($limit);

        $isMultiple = $limit == 1;

        $sql = "
            SELECT
                {$selectedColumns}    
            FROM \"{$table}\"
            WHERE {$condition}
            ORDER BY \"id\" DESC
            {$queryLimit};
        ";

        return $this->get($sql, true, $ttl);
    }

    /**
     * Get One Items From Data Base By Condition
     *
     * @param string $table           Data Base Table
     * @param array  $selectedColumns List Of Returned Data Base Table Columns
     * @param array  $condition       Data Base Selection Condition
     * @param int    $ttl             Time To Live Data Base Cache
     *
     * @return array Item Data
     */
    public function getOneByCondition(
        string $table           = '',
        array  $selectedColumns = [],
        string $condition       = 'true',
        int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
        $selectedColumns = $this->_prepareSelectedColumns($selectedColumns);

        $sql = "
            SELECT
                {$selectedColumns}    
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
     * @param string $table           Data Base Table
     * @param array  $selectedColumns List Of Returned Data Base Table Columns
     * @param array  $condition       Data Base Selection Condition
     * @param int    $ttl             Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getAllByCondition(
        string $table           = '',
        array  $selectedColumns = [],
        string $condition       = 'true',
        int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
        return $this->getByCondition(
            $table,
            $selectedColumns,
            $condition,
            [],
            $ttl
        );
    }

    /**
     * Get All Items From Data Base Table
     *
     * @param string $table           Data Base Table
     * @param array  $selectedColumns List Of Returned Data Base Table Column
     * @param int    $ttl             Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getAll(
        string $table           = '',
        array  $selectedColumns = [],
        int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
        return $this->getAllByCondition(
            $table,
            $selectedColumns,
            'true',
            $ttl
        );
    }

    /**
     * Get Items From Data Base By Condition And Page
     *
     * @param string $table           Data Base Table
     * @param array  $selectedColumns List Of Returned Data Base Table Columns
     * @param array  $condition       Data Base Selection Condition
     * @param int    $page            Page Number
     * @param int    $ttl             Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getByPageWithCondition(
        string $table           = '',
        array  $selectedColumns = [],
        string $condition       = 'true',
        int    $page            = 1,
        int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
        $limit = $this->getQueryLimitByPage($page);

        return $this->getByCondition(
            $table,
            $selectedColumns,
            $condition,
            $limit,
            $ttl
        );
    }

    /**
     * Get All Items From Data Base By Page
     *
     * @param string $table           Data Base Table
     * @param array  $selectedColumns List Of Returned Data Base Table Columns
     * @param int    $page            Page Number
     * @param int    $ttl             Time To Live Data Base Cache
     *
     * @return array List Of Items
     */
    public function getAllByPage(
        string $table           = '',
        array  $selectedColumns = [],
        int    $page            = 1,
        int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
        return $this->getByPageWithCondition(
            $table,
            $selectedColumns,
            'true',
            $page,
            $ttl
        );
    }

    /**
     * Get One Item From Data Base By ID
     *
     * @param string $table Data Base Table
     * @param int    $id    Item ID
     * @param int    $ttl   Time To Live Data Base Cache
     *
     * @return array Item Data
     */
    public function getByID(
        string $table = '',
        int    $id    = -1,
        int    $ttl   = self::DB_DEFAULT_TTL
    ) : array
    {
        $condition = "\"id\" = {$id}";

        return $this->getOneByCondition(
            $table,
            [],
            $condition,
            $ttl
        );
    }

    /**
     * Get One Item From Data Base By Slug
     *
     * @param string $table Data Base Table
     * @param string $slug  Item Slug
     * @param int    $ttl   Time To Live Data Base Cache
     *
     * @return array Item Data
     */
    public function getBySlug(
        string $table = '',
        string $slug  = '',
        int    $ttl   = self::DB_DEFAULT_TTL
    ) : array
    {
        $condition = "\"slug\" = '{$slug}'";

        return $this->getOneByCondition(
            $table,
            [],
            $condition,
            $ttl
        );
    }

    /**
     * Remove Item From Data Base By ID
     *
     * @param string $table Data Base Table
     * @param int    $id    Item ID
     *
     * @return bool Is Item Successfully Removed
     */
    public function removeByID(string $table = '', int $id = -1) : bool
    {
        $condition = "\"id\" = {$id}";
        return $this->remove($table, $condition);
    }

    /**
     * Get Maximum ID In Data Base Table
     *
     * @param string $table Data Base Table
     *
     * @return int Maximum ID In Data Base Table
     */
    public function getMaxID(string $table = '') : int
    {
        $sql = "
            SELECT
                MAX(\"id\") AS max_id
            FROM \"{$table}\"
            GROUP BY \"id\";
        ";
        $res = $this->get($sql, false, self::DB_DEFAULT_TTL);

        if (!array_key_exists('max_id', $res)) {
            return 0;
        }

        return (int) $res['max_id'];
    }

    /**
     * Count Items In Data Base Table By Condition
     *
     * @param string $table     Data Base Table
     * @param array  $condition Data Base Selection Condition
     * @param int    $ttl       Time To Live Data Base Cache
     *
     * @return int Count Items In Data Base Table
     */
    public function count(
        string $table     = '',
        string $condition = 'true',
        int    $ttl       = self::DB_DEFAULT_TTL
    ) : int
    {
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
     * @param string $table Data Base Table
     * @param int    $ttl   Time To Live Data Base Cache
     *
     * @return int Count Items In Data Base Table
     */
    public function countAll(
        string $table = '',
        int    $ttl   = self::DB_DEFAULT_TTL
    ) : int
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
    public function getQueryLimitByPage(int $page = 1) : array
    {
        if ($page < 1) {
            throw new Exception("Invalid Content Page Value");
        }

        $limit = $this->itemsOnPage;
        $offset = $this->itemsOnPage * ($page - 1);

        return [$limit, $offset];
    }

    /**
     * Get SQL SELECT List Of Columns From List Of Columns
     *
     * @param array $selected Columns List Of Columns
     *
     * @return string SQL SELECT List Of Columns
     */
    private function _prepareSelectedColumns(
        array $selectedColumns = []
    ) : string
    {
        if (count($selectedColumns)>0) {
            $selectedColumns = implode(',', $selectedColumns);
        } else {
            $selectedColumns = '*';
        }

        return $selectedColumns;
    }

    /**
     * Get SQL SELECT Limit From Limit And Offset Params
     *
     * @param array $limit Limit And Offset Params
     *
     * @return string SQL SELECT Limit
     */
    private function _prepareQueryLimit(array $limit = []) : string
    {
        if (2 !== count($limit)) {
            return '';
        }

        $limit = (int) $limit[0];
        $offset = (int) $limit[1];

        if ($offset < 0) {
            throw new Exception("Invalid SQL OFFSET Value");
        }

        if ($limit < 1) {
            throw new Exception("Invalid SQL LIMIT Value");
        }

        $limitSQL = "LIMIT {$limit}";
        $offsetSQL = "OFFSET {$offset}";

        return "{$limitSQL}\n{$offsetSQL}";
    }
}
?>