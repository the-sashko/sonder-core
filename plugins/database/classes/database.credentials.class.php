<?php
namespace Core\Plugins\Database\Classes;

use Core\Plugins\Database\Interfaces\IDataBaseCredentials;

use Core\Plugins\Database\Exceptions\DatabaseCredentialsException;

/**
 * Plugin For Working With Data Base
 */
class DatabaseCredentials implements IDataBaseCredentials
{
    const DEFAULT_HOST = 'localhost';

    const DEFAULT_PORT = 5433;

    const DEFAULT_CACHE_TYPE = 'mock';

    private $_dsn = null;

    private $_user = null;

    private $_password = null;

    private $_cacheType = null;

    public function __construct(?array $configData = null)
    {
        if (empty($configData)) {
            $exceptionClass = new DatabaseCredentialsException();

            throw new DatabaseCredentialsException(
                $exceptionClass::MESSAGE_CREDENTIALS_CONFIG_DATA_IS_EMPTY,
                $exceptionClass::CODE_CREDENTIALS_CONFIG_DATA_IS_EMPTY
            );
        }

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

        if (
            !array_key_exists('host', $configData) ||
            empty($configData['host'])
        ) {
            $configData['host'] = static::DEFAULT_HOST;
        }

        if (
            !array_key_exists('port', $configData) ||
            empty($configData['port'])
        ) {
            $configData['host'] = static::DEFAULT_PORT;
        }

        if (
            array_key_exists('user', $configData) &&
            !empty($configData['user'])
        ) {
            $this->_user = (string) $configData['user'];
        }

        if (
            array_key_exists('password', $configData) &&
            !empty($configData['password'])
        ) {
            $this->_password = (string) $configData['password'];
        }

        if (
            !array_key_exists('cache_type', $configData) ||
            empty($configData['cache_type'])
        ) {
            $configData['cache_type'] = static::DEFAULT_CACHE_TYPE;
        }

        $this->_cacheType = (string) $configData['cache_type'];

        $dsn = '%s:host=%s;port=%d;dbname=%s';

        $this->_dsn = sprintf(
            $dsn,
            (string) $configData['type'],
            (string) $configData['host'],
            (int) $configData['port'],
            (string) $configData['db']
        );
    }

    public function getDsn(): string
    {
        if (empty($this->_dsn)) {
            throw new DatabaseCredentialsException(
                DatabaseCredentialsException::MESSAGE_CREDENTIALS_DSN_IS_EMPTY,
                DatabaseCredentialsException::CODE_CREDENTIALS_DSN_IS_EMPTY
            );
        }

        return $this->_dsn;
    }

    public function getUser(): ?string
    {
        return $this->_user;
    }

    public function getPassword(): ?string
    {
        return $this->_password;
    }

    public function getCacheType(): ?string
    {
        return $this->_cacheType;
    }
}
