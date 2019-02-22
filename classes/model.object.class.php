<?php
/**
 * summary
 */
class ModelObjectCore extends DBObjectClass
{
    public $itemsOnPage = 10;

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
        $selectedColumns = $this->_prepareSelectedColums($selectedColumns);
        $queryLimit      = $this->_prepareQueryLimit($limit);

        $isMultiple = $limit == 1;
        $distinct = $isMultiple ? '' : 'DISTINCT';

        $sql = "
            SELECT {$distinct}
                {$selectedColumns}    
            FROM \"{$table}\"
            WHERE {$condition}
            {$queryLimit};
        ";
        return $this->get($sql, $isMultiple, $ttl);
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
        return getByCondition($table, $selectedColumns, $condition, [], $ttl);
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
        $selectedColumns = $this->_prepareSelectedColums($selectedColumns);
        $limit      = $this->_getQueryLimitByPage($page);

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
    public function getOneByCondition(
        string $table           = '',
        array  $selectedColumns = [],
        string $condition       = 'false',
        int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
        return $this->getByCondition(
            $table,
            $selectedColumns,
            $condition,
            [1, 0],
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
    public function removeByID(string $table = '', int $id = -1) : bool
    {
        $condition = "\"id\" = {$id}";
        return $this->remove($sql, $condition);
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
    private function _prepareQueryLimit(array $limit = []) : string
    {
        if (strlen($limit) != 2) {
            return '';
        }

        $offset = (int) $limit[0];
        $limit = (int) $limit[1];

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
    private function _getQueryLimitByPage(int $page = 1) : array
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