<?php

namespace Sonder\Plugins;

use Exception;
use Predis\Autoloader;
use Predis\Client;

final class RedisPlugin
{
    const CONFIG_FILE_PATH = __DIR__ . '/../../../config/redis.json';

    /**
     * @var Client
     */
    private Client $_client;

    /**
     * @var string|null
     */
    private ?string $_keyPrefix = null;

    /**
     * @throws Exception
     */
    final public function __construct()
    {
        $settings = $this->_getSettings();

        Autoloader::register();

        $this->_client = new Client($settings);
    }

    final public function __destruct()
    {
        $this->_client->disconnect();
    }

    /**
     * @param string|null $keyPrefix
     */
    final public function setKeyPrefix(?string $keyPrefix = null): void
    {
        if (!empty($keyPrefix)) {
            $this->_keyPrefix = $keyPrefix;
        }
    }

    /**
     * @param string|null $key
     * @param string|null $value
     * @param int|null $ttl
     *
     * @return bool
     */
    final public function set(
        ?string $key = null,
        ?string $value = null,
        ?int    $ttl = null
    ): bool
    {
        if (empty($key)) {
            return false;
        }

        if (empty($value)) {
            return false;
        }

        $key = $this->_getKey($key);

        if (!empty($ttl)) {
            $this->_client->set($key, $value, 'EX', $ttl);

            return true;
        }

        $this->_client->set($key, $value);

        return true;
    }

    /**
     * @param string|null $key
     *
     * @return string|null
     */
    final public function get(?string $key = null): ?string
    {
        if (empty($key)) {
            return null;
        }

        $key = $this->_getKey($key);

        return $this->_client->get($key);
    }

    /**
     * @param string|null $key
     *
     * @return bool
     */
    final public function remove(?string $key = null): bool
    {
        if (empty($key)) {
            return false;
        }

        $key = $this->_getKey($key);

        return (bool)$this->_client->del($key);
    }

    /**
     * @param string|null $channel
     * @param string|null $message
     */
    final public function sendToChannel(
        ?string $channel = null,
        ?string $message = null
    ): void
    {
        if (!empty($channel) && !empty($message)) {
            $this->_client->publish($channel, $message);
        }
    }

    /**
     * @param string|null $keyPattern
     *
     * @return bool
     */
    final public function removeByPattern(?string $keyPattern = null): bool
    {
        if (empty($keyPattern)) {
            return false;
        }

        $keyPattern = $this->_getKey($keyPattern);

        $keys = $this->_client->keys($keyPattern);

        if (empty($keys)) {
            return false;
        }

        array_map([$this->_client, 'del'], $keys);

        return true;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function _getSettings(): array
    {
        $settings = $this->_getRedisConfig();

        if (!$this->_validateSettings($settings)) {
            throw new Exception('Redis Config Has Bad Format');
        }

        if (array_key_exists('key_prefix', $settings)) {
            $this->_keyPrefix = $settings['key_prefix'];
            unset($settings['key_prefix']);
        }

        return $settings;
    }

    /**
     * @param array|null $settings
     *
     * @return bool
     */
    private function _validateSettings(?array $settings = null): bool
    {
        if (empty($settings)) {
            return false;
        }

        if (!array_key_exists('scheme', $settings)) {
            return false;
        }

        if (!array_key_exists('host', $settings)) {
            return false;
        }

        if (!array_key_exists('port', $settings)) {
            return false;
        }

        if (!array_key_exists('password', $settings)) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    private function _getRedisConfig(): array
    {
        $configFilePath = RedisPlugin::CONFIG_FILE_PATH;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $configFilePath = sprintf(
                '%s/config/redis.json',
                APP_PROTECTED_DIR_PATH
            );
        }

        if (!file_exists($configFilePath) || !is_file($configFilePath)) {
            throw new Exception('Redis Config Not Found');
        }

        $settings = file_get_contents($configFilePath);

        return (array)json_decode($settings, true);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function _getKey(string $key): string
    {
        if (!empty($this->_keyPrefix)) {
            return sprintf('%s:%s', $this->_keyPrefix, $key);
        }

        return $key;
    }
}
