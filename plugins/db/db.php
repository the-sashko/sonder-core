<?php
/**
 * Class For Working With Data Base
 */
class DB
{
    public $dbConfig = [];
    public $dbInstance = NULL;
    public $dbCache = NULL;

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function initDB(array $config = []) : void
    {
        $this->config = $config;
        $this->_setDBCache();
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function _setDBInstance() : void
    {
        list(
            $dsn,
            $user,
            $password
        ) = $this->_getDBCredentials();

        $this->dbInstance = $this->_dbConnect($dsn, $user, $password);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function __destruct()
    {
        $this->dbInstance = NULL;
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _setDBCache() : void
    {
        $cacheProvider = $this->_getDBCacheProvider();
        $this->dbCache = new DBCache($cacheProvider);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _dbConnect(
        string $dsn = '',
        string $user = '',
        string $password = ''
    ) : PDO
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            return new PDO($dsn, $user, $password, $options);
        } catch (PDOException $error) {
            $error = "
                Could not connect to database!
                Error: \"{$error}\"";
            $this->_dbError($error);
        }
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function select(
        string $sql   = '',
        string $scope = 'default',
        int    $ttl   = -1
    ) : array
    {
        $res = $this->dbCache->get($sql, $scope, $ttl);

        if (count($res) > 0) {
            return $res;
        }

        if ($this->dbInstance == NULL) {
            $this->_setDBInstance();
        }

        try {
            $res = $this->dbInstance->query($sql);
            $res = (array) $res->fetchALL();

            $this->dbCache->set($sql, $res, $scope, $ttl);

            return $res;
        } catch (PDOException $error) {
            $error = "
                SQL query failed!
                Error: \"{$error->getMessage()}\"
                Query: \"{$sql}\"
            ";
            $this->_dbError($error);
        }
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function query(
        string $sql   = '',
        string $scope = 'default'
    ) : bool
    {
        $scope = $scope != '' ? $scope : 'default';

        if ($this->dbInstance == NULL) {
            $this->_setDBInstance();
        }

        try {
            $res = (bool) $this->dbInstance->query($sql);

            $this->dbCache->flush($scope);

            return $res;
        } catch (PDOException $error) {
            $error = "
                SQL query failed!
                Error: \"{$error->getMessage()}\"
                Query: \"{$sql}\"
            ";
            $this->_dbError($error);
        }
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function transactionStart() : bool
    {
        $sql = 'START TRANSACTION;';
        return $this->_transaction($sql);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function transactionCommit() : bool
    {
        $sql = 'COMMIT;';
        return $this->_transaction($sql);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    public function transactionRollback() : bool
    {
        $sql = 'ROLLBACK;';
        return $this->_transaction($sql);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getDBCredentials() : array
    {
        $config = $this->config;

        $type = isset($config['type']) ? $config['type'] : 'mysql';
        $host = isset($config['host']) ? $config['host'] : 'localhost';
        $port = isset($config['port']) ? $config['port'] : '5433';
        $db = isset($config['db']) ? $config['db'] : 'default';
        $user = isset($config['user']) ? $config['user'] : '';
        $password = isset($config['password']) ? $config['password'] : '';

        $dsn = "{$type}:host={$host};port={$port};dbname={$db}";

        return [$dsn, $user, $password];
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _getDBCacheProvider() : string
    {
        if (!array_key_exists('cache_provider', $this->config)) {
            return 'mock';
        }

        return (string) $this->config['cache_provider'];
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _dbError(string $error = '') : void
    {
        throw new Exception($error, 500);
    }

    /**
     * Function Name
     *
     * @param type $value Value
     *
     * @return type Value
     */
    private function _transaction(string $transactionSQL = '') : bool
    {
        if ($this->dbInstance == NULL) {
            $this->_setDBInstance();
        }

        try {
            $res = (bool) $this->dbInstance->query($transactionSQL);

            return $res;
        } catch (PDOException $error) {
            $error = "
                SQL query failed!
                Error: \"{$error->getMessage()}\"
                Query: \"{$sql}\"
            ";
            $this->_dbError($error);
        }
    }
}
?>