<?php

namespace Sonder\Plugins;

use PDO;
use PDOException;
use Sonder\Plugins\Database\Classes\DatabaseCache;
use Sonder\Plugins\Database\Classes\DatabaseCredentials;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabaseCredentialsException;
use Sonder\Plugins\Database\Exceptions\DatabaseException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\Database\Interfaces\IDatabasePlugin;

final class DatabasePlugin implements IDatabasePlugin
{
    const DEFAULT_SCOPE = 'default';

    /**
     * @var DatabaseCache|null
     */
    private ?DatabaseCache $_cache = null;

    /**
     * @var PDO|null
     */
    private ?PDO $_instance = null;

    final public function __destruct()
    {
        $this->_instance = null;
    }

    /**
     * @param array|null $configData
     *
     * @throws DatabaseCacheException
     * @throws DatabaseCredentialsException
     * @throws DatabasePluginException
     */
    final public function connect(?array $configData = null): void
    {
        if (empty($configData)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_CONFIG_DATA_IS_EMPTY,
                DatabaseException::CODE_PLUGIN_CONFIG_DATA_IS_EMPTY
            );
        }

        $credentials = new DatabaseCredentials($configData);

        $this->_cache = new DatabaseCache($credentials->getCacheType());

        $dsn = $credentials->getDsn();
        $user = $credentials->getUser();
        $password = $credentials->getPassword();

        if (empty($dsn)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_DSN_IS_EMPTY,
                DatabaseException::CODE_PLUGIN_DSN_IS_EMPTY
            );
        }

        if (empty($user)) {
            $user = null;
        }

        if (empty($password)) {
            $password = null;
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->_instance = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $exp) {
            $errorMessage = sprintf(
                '%s. Error: %s',
                DatabasePluginException::MESSAGE_PLUGIN_CAN_NOT_CONNECT,
                $exp->getMessage()
            );

            throw new DatabasePluginException(
                $errorMessage,
                DatabaseException::CODE_PLUGIN_CAN_NOT_CONNECT
            );
        }
    }

    /**
     * @param string|null $sql
     * @param string|null $scope
     * @param int|null $ttl
     *
     * @return array|null
     *
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     */
    final public function select(
        ?string $sql = null,
        ?string $scope = null,
        ?int    $ttl = null
    ): ?array
    {
        if (empty($sql)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_SQL_IS_EMPTY,
                DatabaseException::CODE_PLUGIN_SQL_IS_EMPTY
            );
        }

        if (empty($scope)) {
            $scope = DataBasePlugin::DEFAULT_SCOPE;
        }

        if (null === $this->_instance) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_NOT_CONNECTED,
                DatabaseException::CODE_PLUGIN_NOT_CONNECTED
            );
        }

        $cachedRows = $this->_cache->get($sql, $scope);

        if (!empty($cachedRows)) {
            return $cachedRows;
        }

        try {
            if (defined('DEBUG_SQL')) {
                echo sprintf("\n%s\n", $sql);
            }

            $rows = (array)$this->_instance->query($sql)->fetchALL();

            if (empty($rows)) {
                $rows = null;
            }

            $this->_cache->set($sql, $rows, $scope, $ttl);

            return $rows;
        } catch (PDOException $exp) {
            $errorMessage = DatabasePluginException::MESSAGE_PLUGIN_SQL_ERROR;

            $errorMessage = sprintf(
                '%s. Error: "%s". Query: %s"',
                $errorMessage,
                $exp->getMessage(),
                $sql
            );

            $this->_error($errorMessage);
        }

        return null;
    }

    /**
     * @param string|null $sql
     * @param string|null $scope
     *
     * @return bool
     *
     * @throws DatabasePluginException
     */
    final public function query(
        ?string $sql = null,
        ?string $scope = null
    ): bool
    {
        if (empty($sql)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_SQL_IS_EMPTY,
                DatabaseException::CODE_PLUGIN_SQL_IS_EMPTY
            );
        }

        if (empty($scope)) {
            $scope = DataBasePlugin::DEFAULT_SCOPE;
        }

        if (null === $this->_instance) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_NOT_CONNECTED,
                DatabaseException::CODE_PLUGIN_NOT_CONNECTED
            );
        }

        try {
            if (defined('DEBUG_SQL')) {
                echo sprintf("\n%s\n", $sql);
            }

            $result = (bool)$this->_instance->query($sql);

            $this->_cache->clean($scope);

            return $result;
        } catch (PDOException $exp) {
            $errorMessage = DatabasePluginException::MESSAGE_PLUGIN_SQL_ERROR;

            $errorMessage = sprintf(
                '%s. Error: "%s". Query: %s"',
                $errorMessage,
                $exp->getMessage(),
                $sql
            );

            $this->_error($errorMessage);
        }

        return false;
    }

    /**
     * @return bool
     *
     * @throws DatabasePluginException
     */
    final public function transactionStart(): bool
    {
        $sql = 'START TRANSACTION;';

        return $this->_transactionQuery($sql);
    }

    /**
     * @return bool
     *
     * @throws DatabasePluginException
     */
    final public function transactionCommit(): bool
    {
        $sql = 'COMMIT;';

        return $this->_transactionQuery($sql);
    }

    /**
     * @return bool
     *
     * @throws DatabasePluginException
     */
    final public function transactionRollback(): bool
    {
        $sql = 'ROLLBACK;';

        return $this->_transactionQuery($sql);
    }

    /**
     * @param string|null $sql
     * @return bool
     *
     * @throws DatabasePluginException
     */
    private function _transactionQuery(?string $sql = null): bool
    {
        if (empty($sql)) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_SQL_IS_EMPTY,
                DatabaseException::CODE_PLUGIN_SQL_IS_EMPTY
            );
        }

        if (null === $this->_instance) {
            throw new DatabasePluginException(
                DatabasePluginException::MESSAGE_PLUGIN_NOT_CONNECTED,
                DatabaseException::CODE_PLUGIN_NOT_CONNECTED
            );
        }

        try {
            if (defined('DEBUG_SQL')) {
                echo sprintf("\n%s\n", $sql);
            }

            return (bool)$this->_instance->query($sql);
        } catch (PDOException $exp) {
            $errorMessage = DatabasePluginException::MESSAGE_PLUGIN_SQL_ERROR;

            $errorMessage = sprintf(
                '%s. Error: "%s". Query: %s"',
                $errorMessage,
                $exp->getMessage(),
                $sql
            );

            $this->_error($errorMessage);
        }

        return false;
    }

    /**
     * @param string|null $errorMessage
     *
     * @throws DatabasePluginException
     */
    private function _error(?string $errorMessage = null): void
    {
        $errorCode = DatabaseException::CODE_PLUGIN_SQL_ERROR;

        if (empty($errorMessage)) {
            $errorMessage = $this->_getDefaultErrorMessage();
            $errorCode = DatabaseException::CODE_PLUGIN_UNKNOWN_ERROR;
        }

        throw new DatabasePluginException($errorMessage, $errorCode);
    }

    /**
     * @return string
     */
    private function _getDefaultErrorMessage(): string
    {
        return DatabasePluginException::MESSAGE_PLUGIN_UNKNOWN_ERROR;
    }
}
