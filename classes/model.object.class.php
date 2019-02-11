<?php
/**
 * summary
 */
class ModelObjectCore extends DBObjectClass
{

	const DB_CONFIG_PATH = __DIR__.'/../../config/db.json';
	const DB_CACHE_DIR = __DIR__.'/../../res/cache/db/';

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
    	string $condition       = 'false',
    	bool   $isMultiple      = true,
    	int    $ttl             = self::DB_DEFAULT_TTL
    ) : array
    {
    	$selectedColumns = $this->_prepareSelectedColums($selectedColumns);

    	$distinct = $isMultiple ? '' : 'DISTINCT';
    	$sql = "
    		SELECT {$distinct}
    			{$selectedColumns}	
    		FROM `{$table}`
    		WHERE {$condition};
    	";
    	return $this->get($sql, $isMultiple, $ttl);
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
    		false,
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
    	$condition = "`id` = {$id}";

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
        $condition = "`id` = {$id}";
        return $this->remove($sql, $condition);
    }


    /**
     * summary
     */
    private function _prepareSelectedColums(
    	array $selectedColumns = []
    ) : string
    {
    	if (count($selectedColumns)>0) {
    		$selectedColumns = implode("`,`", $selectedColumns);
    		$selectedColumns = "`{$selectedColumns}`";
    	} else {
    		$selectedColumns = '*';
    	}

    	return $selectedColumns;
    }

}
?>