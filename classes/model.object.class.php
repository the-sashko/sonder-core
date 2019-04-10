<?php
/**
 * summary
 */
class ModelObjectCore extends DBObjectClass
{
    public $itemsOnPage = 10;
    public $defaultTableName = NULL;

    /**
     * summary
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
     * summary
     */
    public function getOne(
        string $sql = '',
        int    $ttl = self::DB_DEFAULT_TTL
    ) : array
    {
        return $this->get($sql, false, $ttl);
    }

    /**
     * summary
     */
    public function getByCondition(
        string $table           = '',
        array  $selectedColumns = [],
        string $condition       = 'true',
        array  $limit           = [],
        int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
        $selectedColumns = $this->_prepareSelectedColums(
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
     * summary
     */
    public function getOneByCondition(
        string $table           = '',
        array  $selectedColumns = [],
        string $condition       = 'true',
        int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
        $selectedColumns = $this->_prepareSelectedColums($selectedColumns);

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
     * summary
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
     * summary
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
     * summary
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
     * summary
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
     * summary
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
     * summary
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
     * summary
     */
    public function vremoveByID(string $table = '', int $id = -1) : bool
    {
        $condition = "\"id\" = {$id}";
        return $this->remove($table, $condition);
    }

    /**
     * summary
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
     * summary
     */
    private function _prepareSelectedColums(
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
     * summary
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
     * summary
     */
    public function countAll(
        string $table = '',
        int    $ttl   = self::DB_DEFAULT_TTL
    ) : int
    {
        return $this->count($table, 'true', $ttl);
    }

    /**
     * summary
     */
    private function _prepareQueryLimit(array $limit = []) : string
    {
        if (count($limit) != 2) {
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

    /**
     * summary
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
}
?>
