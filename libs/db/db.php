<?php

/**
 * Trait for working with database
 */

trait DB
{

    /**
     * summary
     */
    private function _dbConnect() : PDO
    {
        list($dsn, $user, $password) = $this->_getDBConnetionData();

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try{
            $pdo = new PDO($dsn, $user, $password, $options);
            return $pdo;
        } catch (PDOException $error){
            $error = "
                Could not connect to database!
                Error: \"{$error}\"";
            $this->_dbError($error);
        }
    }

    /**
     * summary
     */
    public function select(
        string $sql   = '',
        string $scope = 'default',
        int    $ttl   = -1
    ) : array
    {
        $scope = $scope != '' ? $scope : 'default';
        $cacheFilePath = $this->_getCacheFilePath($sql, $scope);

        if($ttl>0){
            $res = $this->_getDataFromCache($cacheFilePath);
            if (count($res) > 0) {
                return $res;
            }
        }

        $pdo = $this->_dbConnect();

        if($pdo === 0){
            $pdo = NULL;
            return [];
        } else {
            try{
                $res = $pdo->query($sql);
                $pdo = NULL;
                $res = (array) $res->fetchALL();
                if($ttl>0){
                    $this->_setDataFromCache(
                        $res,
                        $cacheFilePath,
                        $scope,
                        $ttl
                    );
                }
                return $res;
            } catch (PDOException $error){
                $error = "
                    SQL query failed!
                    Error: \"{$error->getMessage()}\"
                    Query: \"{$sql}\"
                ";
                $this->_dbError($error);
            }
        }
    }

    /**
     * summary
     */
    public function query(
        string $sql   = '',
        string $scope = 'default'
    ) : bool
    {
        $scope = $scope != '' ? $scope : 'default';
        $pdo = $this->_dbConnect();
        if($pdo === 0){
            $pdo = NULL;
            return false;
        } else {
            try{
                $res = (bool) $pdo->query($sql);
                $pdo = NULL;
                $this->_removeCache();
                return $res;
            } catch (PDOException $error){
                echo "
                    SQL query failed!
                    Error: \"{$error->getMessage()}\"
                    Query: \"{$sql}\"
                ";
                $this->_dbError($error);
            }
        }
    }

    /**
     * summary
     */
    public function transactionStart() : bool
    {
        $sql = 'START TRANSACTION;';
        return $this->_transaction($sql);
    }

    /**
     * summary
     */
    public function transactionCommit() : bool
    {
        $sql = 'COMMIT;';
        return $this->_transaction($sql);
    }

    /**
     * summary
     */
    public function transactionRollback() : bool
    {
        $sql = 'ROLLBACK;';
        return $this->_transaction($sql);
    }

    /**
     * summary
     */
    private function _getDBConnetionData() : array
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
     * summary
     */
    private function _getCacheFilePath(
        string $sql = '',
        string $scope = ''
    ) : string
    {
        $hash = hash('md5', $sql).
                hash('sha512', $sql).
                hash('md5', $scope.$sql);
        return $this::DB_CACHE_DIR.$scope.'/'.$hash;
    }

    /**
     * summary
     */
    private function _getDataFromCache(string $cacheFilePath = '') : array
    {
        $res = [];

        if (is_file($cacheFilePath)) {
            $cacheData = file_get_contents($cacheFilePath);
            $cacheData = json_decode($cacheData,true);
            if(
                isset($cacheData['time']) &&
                intval($cacheData['time']) > time() &&
                isset($cacheData['content'])
            ){
                $res = base64_decode($cacheData['content']);
                $res = json_decode($res,true);
            } else {
                unlink($cacheFilePath);
            }
        }

        return $res;
    }

    /**
     * summary
     */
    private function _setDataFromCache(
        array  $res           = [],
        string $cacheFilePath = '',
        string $scope         = '',
        int    $ttl           = -1
    ) : void
    {
        $content = base64_encode(json_encode($res));

        $cacheData = [
            'time' => time()+$ttl,
            'content' => $content
        ];

        if(is_file($cacheFilePath)){
            unlink($cacheFilePath);
        }

        if(!is_dir($this::DB_CACHE_DIR.$scope)){
            mkdir($this::DB_CACHE_DIR.$scope);
            chmod($this::DB_CACHE_DIR.$scope, 0775);
        }

        file_put_contents($cacheFilePath, json_encode($cacheData));
        chmod($cacheFilePath, 0775);
    }

    /**
     * summary
     */
    private function _removeCache(string $scope = '') : void
    {
        if (is_dir($this::DB_CACHE_DIR.$scope)) {
            foreach(scandir($this::DB_CACHE_DIR) as $fileItem){
                if(
                    $fileItem!='.' &&
                    $fileItem!='..' &&
                    is_file($this::DB_CACHE_DIR.$scope.'/'.$fileItem)
                ){
                    unlink($this::DB_CACHE_DIR.$scope.'/'.$fileItem);
                }
            }
        }
    }

    /**
     * summary
     */
    private function _dbError(string $error = '') : void
    {
        throw new Exception($error, 500);
    }

    /**
     * summary
     */
    private function _transaction(string $transactionSQL = '') : bool
    {
        $pdo = $this->_dbConnect();
        if($pdo === 0){
            $pdo = NULL;
            return false;
        } else {
            try{
                $res = (bool) $pdo->query($transactionSQL);
                $pdo = NULL;
                return $res;
            } catch (PDOException $error){
                echo "
                    SQL query failed!
                    Error: \"{$error->getMessage()}\"
                    Query: \"{$sql}\"
                ";
                $this->_dbError($error);
            }
        }
    }
}
?>