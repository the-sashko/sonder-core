<?php

namespace Sonder\Plugins\Database\Classes;

use Sonder\Plugins\Database\Exceptions\DatabaseCredentialsException;
use Sonder\Plugins\Database\Exceptions\DatabaseException;
use Sonder\Plugins\Database\Interfaces\IDataBaseCredentials;

final class DatabaseCredentials implements IDataBaseCredentials
{
    const DEFAULT_HOST = 'localhost';

    const DEFAULT_PORT = 5433;

    const DEFAULT_CACHE_TYPE = 'mock';

    /**
     * @var string|null
     */
    private ?string $_dsn = null;

    /**
     * @var string|null
     */
    private ?string $_user = null;

    /**
     * @var string|null
     */
    private ?string $_password = null;

    /**
     * @var string|null
     */
    private ?string $_cacheType = null;

    /**
     * @param array|null $configData
     *
     * @throws DatabaseCredentialsException
     */
    final public function __construct(?array $configData = null)
    {
        if (empty($configData)) {
            $exceptionClass = new DatabaseCredentialsException();

            throw new DatabaseCredentialsException(
                $exceptionClass::MESSAGE_CREDENTIALS_CONFIG_DATA_IS_EMPTY,
                $exceptionClass::CODE_CREDENTIALS_CONFIG_DATA_IS_EMPTY
            );
        }

        $this->_setDsnFromConfig($configData);
        $this->_setUserFromConfig($configData);
        $this->_setPasswordFromConfig($configData);
        $this->_setCacheTypeFromConfig($configData);
    }

    /**
     * @return string
     *
     * @throws DatabaseCredentialsException
     */
    final public function getDsn(): string
    {
        if (empty($this->_dsn)) {
            throw new DatabaseCredentialsException(
                DatabaseCredentialsException::MESSAGE_CREDENTIALS_DSN_IS_EMPTY,
                DatabaseException::CODE_CREDENTIALS_DSN_IS_EMPTY
            );
        }

        return $this->_dsn;
    }

    /**
     * @return string|null
     */
    final public function getUser(): ?string
    {
        return $this->_user;
    }

    /**
     * @return string|null
     */
    final public function getPassword(): ?string
    {
        return $this->_password;
    }

    /**
     * @return string|null
     */
    final  public function getCacheType(): ?string
    {
        return $this->_cacheType;
    }

    /**
     * @param array $configData
     * @return string
     *
     * @throws DatabaseCredentialsException
     */
    private function _getTypeFromConfig(array $configData): string
    {
        if (
            !array_key_exists('type', $configData) ||
            empty($configData['type'])
        ) {
            $exceptionClass = new DatabaseCredentialsException();

            throw new DatabaseCredentialsException(
                $exceptionClass::MESSAGE_CREDENTIALS_DB_TYPE_IS_NOT_SET,
                $exceptionClass::CODE_CREDENTIALS_DB_TYPE_IS_NOT_SET
            );
        }

        return (string)$configData['type'];
    }

    /**
     * @param array $configData
     * @return string
     *
     * @throws DatabaseCredentialsException
     */
    private function _getDataBaseNameFromConfig(array $configData): string
    {
        if (
            !array_key_exists('db', $configData) ||
            empty($configData['db'])
        ) {
            $exceptionClass = new DatabaseCredentialsException();

            throw new DatabaseCredentialsException(
                $exceptionClass::MESSAGE_CREDENTIALS_DB_NAME_IS_NOT_SET,
                $exceptionClass::CODE_CREDENTIALS_DB_NAME_IS_NOT_SET
            );
        }

        return (string)$configData['db'];
    }

    /**
     * @param array $configData
     *
     * @return string
     */
    private function _getHostFromConfig(array $configData): string
    {
        if (
            !array_key_exists('host', $configData) ||
            empty($configData['host'])
        ) {
            return DatabaseCredentials::DEFAULT_HOST;
        }

        return (string)$configData['host'];
    }

    /**
     * @param array $configData
     *
     * @return int
     */
    private function _getPortFromConfig(array $configData): int
    {
        if (
            !array_key_exists('port', $configData) ||
            empty($configData['port'])
        ) {
            return DatabaseCredentials::DEFAULT_PORT;
        }

        return (int)$configData['port'];
    }

    /**
     * @param array $configData
     */
    private function _setUserFromConfig(array $configData): void
    {
        if (
            array_key_exists('user', $configData) &&
            !empty($configData['user'])
        ) {
            $this->_user = (string)$configData['user'];
        }
    }

    /**
     * @param array $configData
     */
    private function _setPasswordFromConfig(array $configData): void
    {
        if (
            array_key_exists('password', $configData) &&
            !empty($configData['password'])
        ) {
            $this->_password = (string)$configData['password'];
        }
    }

    /**
     * @param array $configData
     */
    private function _setCacheTypeFromConfig(array $configData): void
    {
        $cacheType = DatabaseCredentials::DEFAULT_CACHE_TYPE;

        if (
            !array_key_exists('cache_type', $configData) ||
            empty($configData['cache_type'])
        ) {
            $cacheType = (string)$configData['cache_type'];
        }

        $this->_cacheType = $cacheType;
    }

    /**
     * @param array $configData
     *
     * @throws DatabaseCredentialsException
     */
    private function _setDsnFromConfig(array $configData): void
    {
        $type = $this->_getTypeFromConfig($configData);
        $databaseName = $this->_getDataBaseNameFromConfig($configData);
        $host = $this->_getHostFromConfig($configData);
        $port = $this->_getPortFromConfig($configData);

        $dsn = '%s:host=%s;port=%d;dbname=%s';

        $this->_dsn = sprintf($dsn, $type, $host, $port, $databaseName);
    }
}
