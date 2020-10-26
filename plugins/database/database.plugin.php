<?php
use Core\Plugins\Database\Classes\DatabaseCredentials;
use Core\Plugins\Database\Classes\DatabaseCache;

use Core\Plugins\Database\Interfaces\IDataBasePlugin;

use Core\Plugins\Database\Exceptions\DatabasePluginException;

/**
 * Plugin For Working With Data Base
 */
class DataBasePlugin implements IDataBasePlugin
{
    const DEFAULT_SCOPE = 'default';

    private $_credentials = null;

    private $_cache = null;

    /**
     * @var PDO|null Instance
     */
    private $_instance = null;

    public function __destruct()
    {
        $this->_instance = null;
    }

    public function initDB(?array $configData = null): void
    {
        if (empty($configData)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_CONFIG_DATA_IS_EMPTY,
                DatabasePluginException::CODE_PLUGIN_CONFIG_DATA_IS_EMPTY
            );
        }

        $this->_credentials = new DatabaseCredentials($configData);

        $this->_cache       = new DatabaseCache(
            $this->_credentials->getCacheType()
        );
    }

    /**
     * Execute SQL SELECT Query
     *
     * @param string|null $sql   SQL SELECT Query
     * @param string|null $scope Scope Of SQL Query
     * @param int|null    $ttl   Data Base Cache Time To Live
     *
     * @return array|null Data From Data Base
     */
    public function select(
        ?string $sql   = null,
        ?string $scope = null,
        ?int    $ttl   = null
    ): ?array
    {
        if (empty($sql)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_SQL_IS_EMPTY,
                DatabasePluginException::CODE_PLUGIN_SQL_IS_EMPTY
            );
        }

        if (empty($scope)) {
            $scope = static::DEFAULT_SCOPE;
        }

        if (null === $this->_instance) {
            $this->_setInstance();
        }

        $cachedRows = $this->_cache->get($sql, $scope);

        if (!empty($cachedRows)) {
            return $cachedRows;
        }

        try {
            if (defined('DEBUG_SQL')) {
                echo sprintf("\n%s\n", $sql);
            }

            $rows = (array) $this->_instance->query($sql)->fetchALL();

            if (empty($rows)) {
                $rows = null;
            }

            $this->_cache->set($sql, $rows, $scope, $ttl);

            return $rows;
        } catch (\PDOException $exp) {
            $errorMessage = DatabasePluginException::MESSAGE_PLUGIN_SQL_ERROR;

            $errorMessage = sprintf(
                '%s. Error: "%s". Query: %s"',
                $errorMessage,
                $exp->getMessage(),
                $sql
            );

            $this->_error($errorMessage);
        }
    }

    /**
     * Execute SQL Query
     *
     * @param string|null $sql   SQL Query
     * @param string|null $scope Scope Of SQL Query
     *
     * @return bool Is SQL Query Successfully Executed
     */
    public function query(?string $sql = null, ?string $scope = null): bool
    {
        if (empty($sql)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_SQL_IS_EMPTY,
                DatabasePluginException::CODE_PLUGIN_SQL_IS_EMPTY
            );
        }

        if (empty($scope)) {
            $scope = static::DEFAULT_SCOPE;
        }

        if (null === $this->_instance) {
            $this->_setInstance();
        }

        try {
            if (defined('DEBUG_SQL')) {
                echo sprintf("\n%s\n", $sql);
            }

            $result = (bool) $this->_instance->query($sql);

            $this->_cache->clean($scope);

            return $result;
        } catch (\PDOException $exp) {
            $errorMessage = DatabasePluginException::MESSAGE_PLUGIN_SQL_ERROR;

            $errorMessage = sprintf(
                '%s. Error: "%s". Query: %s"',
                $errorMessage,
                $exp->getMessage(),
                $sql
            );

            $this->_error($errorMessage);
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

        return $this->_transactionQuery($sql);
    }

    /**
     * Commit SQL Transaction
     *
     * @return bool Is SQL Query Successfully Commited
     */
    public function transactionCommit(): bool
    {
        $sql = 'COMMIT;';

        return $this->_transactionQuery($sql);
    }

    /**
     * Rollback SQL Transaction
     *
     * @return bool Is SQL Query Successfully Rollback
     */
    public function transactionRollback(): bool
    {
        $sql = 'ROLLBACK;';

        return $this->_transactionQuery($sql);
    }

    /**
     * Connect To Data Base And Set PDO Instance
     */
    private function _setInstance(): void
    {
        $dsn      = $this->_credentials->getDsn();
        $user     = $this->_credentials->getUser();
        $password = $this->_credentials->getPassword();

        if (empty($dsn)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_DSN_IS_EMPTY,
                DatabasePluginException::CODE_PLUGIN_DSN_IS_EMPTY
            );
        }

        if (empty($user)) {
            $user = null;
        }

        if (empty($password)) {
            $password = null;
        }

        $this->_instance = $this->_connect($dsn, $user, $password);
    }

    /**
     * Execute Transaction SQL Query
     *
     * @param string|null $sql SQL Query
     *
     * @return bool Is Transaction SQL Query Successfully Executed
     */
    private function _transactionQuery(?string $sql = null): bool
    {
        if (empty($sql)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_SQL_IS_EMPTY,
                DatabasePluginException::CODE_PLUGIN_SQL_IS_EMPTY
            );
        }

        if (null === $this->_instance) {
            $this->_setInstance();
        }

        try {
            if (defined('DEBUG_SQL')) {
                echo sprintf("\n%s\n", $sql);
            }

            return (bool) $this->_instance->query($sql);
        } catch (\PDOException $exp) {
            $errorMessage = DatabasePluginException::MESSAGE_PLUGIN_SQL_ERROR;

            $errorMessage = sprintf(
                '%s. Error: "%s". Query: %s"',
                $errorMessage,
                $exp->getMessage(),
                $sql
            );

            $this->_error($errorMessage);
        }
    }

    /**
     * Connect To Data Base
     *
     * @param string|null $dsn      Data Base DSN
     * @param string|null $user     Data Base User
     * @param string|null $password Data Base Password
     *
     * @return PDO Instance Of PDO
     */
    private function _connect(
        ?string $dsn      = null,
        ?string $user     = null,
        ?string $password = null
    ): \PDO
    {
        if (empty($dsn)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_DSN_IS_EMPTY,
                DatabasePluginException::CODE_PLUGIN_DSN_IS_EMPTY
            );
        }

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];

        try {
            return new \PDO($dsn, $user, $password, $options);
        } catch (\PDOException $error) {
            $error = "
                Could Not Connect To Database!
                Error: \"{$error}\"";
            $this->_error($error);
        }
    }

    /**
     * Handle Data Base Errors
     *
     * @param string|null $errorMessage Data Base Error Message
     */
    private function _error(?string $errorMessage = null): void
    {
        $errorCode = DatabasePluginException::CODE_PLUGIN_SQL_ERROR;

        if (empty($errorMessage)) {
            $errorMessage = $this->_getDefaultErrorMessage();
            $errorCode    = DatabasePluginException::CODE_PLUGIN_UNKNOWN_ERROR;
        }

        throw new DatabasePluginException($errorMessage, $errorCode);
    }

    /**
     * Get Default Error Message
     *
     * @return string Default Error Message
     */
    private function _getDefaultErrorMessage(): string
    {
        return DatabasePluginException::MESSAGE_PLUGIN_UNKNOWN_ERROR;
    }
}
