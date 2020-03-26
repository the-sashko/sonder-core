<?php
/**
 * Class For Working With Data Base
 */
class DB
{
    /**
     * @var array Data Base Config Data
     */
    public $dbConfig = [];

    /**
     * @var PDO Instance Of PDO
     */
    public $dbInstance = null;

    /**
     * @var object Instance Of Data Base Cache
     */
    public $dbCache = null;

    /**
     * Set Data Base Config And Data Base Cache
     *
     * @param array $config Data Base Config Data
     **/
    public function initDB(array $config = []): void
    {
        $this->config = $config;
        $this->_setDBCache();
    }

    /**
     * Connect To Data Base And Set PDO Instance
     */
    private function _setDBInstance(): void
    {
        list(
            $dsn,
            $user,
            $password
        ) = $this->_getDBCredentials();

        $this->dbInstance = $this->_dbConnect($dsn, $user, $password);
    }

    public function __destruct()
    {
        $this->dbInstance = NULL;
    }

    /**
     * Set Data Base Cache Provider
     */
    private function _setDBCache(): void
    {
        $cacheProvider = $this->_getDBCacheProvider();
        $this->dbCache = new DBCache($cacheProvider);
    }

    /**
     * Connect To Data Base
     *
     * @param string $dsn      Data Base DSN
     * @param string $user     Data Base User
     * @param string $password Data Base Password
     *
     * @return PDO Instance Of PDO
     */
    private function _dbConnect(
        string $dsn      = '',
        string $user     = '',
        string $password = ''
    ): PDO
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
     * Execute SQL SELECT Query
     *
     * @param ?string $sql   SQL SELECT Query
     * @param ?string $scope Scope Of SQL Query
     * @param int     $ttl   Data Base Cache Time To Live
     *
     * @return array Data From Data Base
     */
    public function select(
        ?string $sql   = null,
        ?string $scope = null,
        int     $ttl   = -1
    ): ?array
    {
        if (empty($sql)) {
            return null;
        }

        $res = $this->dbCache->get($sql, $scope, $ttl);

        if (!empty($res)) {
            return $res;
        }

        if (null === $this->dbInstance) {
            $this->_setDBInstance();
        }

        try {
            $res = $this->dbInstance->query($sql);
            $res = (array) $res->fetchALL();

            if (empty($res)) {
                $res = null;
            }

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
     * Execute SQL Query
     *
     * @param ?string $sql   SQL Query
     * @param ?string $scope Scope Of SQL Query
     *
     * @return bool Is SQL Query Successfully Executed
     */
    public function query(?string $sql = null, ?string $scope = null): bool
    {
        if (empty($sql)) {
            return null;
        }

        $scope = empty($scope) ? 'default' : $scope;

        if (null === $this->dbInstance) {
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
     * Start SQL Transaction
     *
     * @return bool Is SQL Transaction Successfully Start
     */
    public function transactionStart(): bool
    {
        $sql = 'START TRANSACTION;';

        return $this->_transaction($sql);
    }

    /**
     * Commit SQL Transaction
     *
     * @return bool Is SQL Query Successfully Commited
     */
    public function transactionCommit(): bool
    {
        $sql = 'COMMIT;';

        return $this->_transaction($sql);
    }

    /**
     * Rollback SQL Transaction
     *
     * @return bool Is SQL Query Successfully Rollback
     */
    public function transactionRollback(): bool
    {
        $sql = 'ROLLBACK;';

        return $this->_transaction($sql);
    }

    /**
     * Get Data Base Credentials
     *
     * @return array Data Base Credentials
     */
    private function _getDBCredentials(): array
    {
        $config = $this->config;

        $type     = isset($config['type']) ? $config['type'] : 'mysql';
        $host     = isset($config['host']) ? $config['host'] : 'localhost';
        $port     = isset($config['port']) ? $config['port'] : '5433';
        $dbName   = isset($config['db']) ? $config['db'] : 'default';
        $user     = isset($config['user']) ? $config['user'] : '';
        $password = isset($config['password']) ? $config['password'] : '';

        $dsn = "{$type}:host={$host};port={$port};dbname={$dbName}";

        return [$dsn, $user, $password];
    }

    /**
     * Get Data Base Cache Provider Name
     *
     * @return string Data Base Cache Provider Name
     */
    private function _getDBCacheProvider(): string
    {
        if (!array_key_exists('cache_provider', $this->config)) {
            return 'mock';
        }

        return (string) $this->config['cache_provider'];
    }

    /**
     * Handle Data Base Errors
     *
     * @param ?string $error Data Base Error
     */
    private function _dbError(?string $error = null): void
    {
        $error = empty($error) ? 'Unknown DataBase Error' : $error;

        throw new Exception($error);
    }

    /**
     * Execute Transaction SQL Query
     *
     * @param ?string $transactionSQL SQL Query
     *
     * @return bool Is Transaction SQL Query Successfully Executed
     */
    private function _transaction(?string $transactionSQL = null): bool
    {
        if (empty($transactionSQL)) {
            return false;
        }

        if (null === $this->dbInstance) {
            $this->_setDBInstance();
        }

        try {
            $res = (bool) $this->dbInstance->query($transactionSQL);

            return $res;
        } catch (PDOException $error) {
            $error = "
                SQL query failed!
                Error: \"{$error->getMessage()}\"
                Query: \"{$transactionSQL}\"
            ";
            $this->_dbError($error);
        }
    }
}
