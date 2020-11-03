<?php
/**
 * Plugin For Working With Redis
 */
class RedisPlugin
{
    /**
     * @var string Redis Config File Path
     */
    const CONFIG_FILE_PATH = __DIR__.'/../../../config/redis.json';

    /**
     * @var object|null Predis Client Instance
     */
    private $_client = null;

    /**
     * @var string|null Redis Entry Key Prefix
     */
    private $_keyPrefix = null;

    public function __construct()
    {
        $settings = $this->_getSettings();

        Predis\Autoloader::register();

        $this->_client = new Predis\Client($settings);
    }

    public function __destruct()
    {
        $this->_client->disconnect();
    }

    /**
     * Get Redis Settings From Config File
     *
     * @return array Redis Settings Data
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
     * Check Is Redis Settings Data Has Valid Format
     *
     * @param array|null $settings Redis Settings Data
     *
     * @return bool Is Redis Settings Data Has Valid Format
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
     * Get Redis Config Data
     *
     * @return array Redis Config Data
     */
    private function _getRedisConfig(): array
    {
        if (
            !file_exists(static::CONFIG_FILE_PATH) ||
            !is_file(static::CONFIG_FILE_PATH)
        ) {
            throw new \Exception('Redis Config Not Found');
        }

        $settings = file_get_contents(static::CONFIG_FILE_PATH);

        return (array) json_decode($settings, true);
    }

    /**
     * Get Redis Entry Key
     *
     * @param string $key Input Redis Entry Key Value
     *
     * @return string Output Redis Entry Key Value
     */
    private function _getKey(string $key): string
    {
        if (!empty($this->_keyPrefix)) {
            return sprintf('%s:%s', $this->_keyPrefix, $key);
        }

        return $key;
    }

    /**
     * Set Redis Entry Key Prefix
     *
     * @param string|null $keyPrefix Redis Entry Key Prefix
     */
    public function setKeyPrefix(?string $keyPrefix = null): void
    {
        if (!empty($keyPrefix)) {
            $this->_keyPrefix = $keyPrefix;
        }
    }

    /**
     * Saving Data To Redis
     *
     * @param string|null $key   Redis Entry Key
     * @param string|null $value Redis Entry Value
     * @param int|null    $ttl   Redis Entry Time To Live
     *
     * @return bool Is Saving Value To Redis Successfully
     */
    public function set(
        ?string $key   = null,
        ?string $value = null,
        ?int    $ttl   = null
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
     * Get Data From Redis
     *
     * @param string|null $key Redis Entry Key
     *
     * @return string|null Redis Entry Value
     */
    public function get(?string $key = null): ?string
    {
        if (empty($key)) {
            return null;
        }

        $key = $this->_getKey($key);

        return (string) $this->_client->get($key);
    }

    /**
     * Removing Data From Redis By Key
     *
     * @param string|null $key Redis Entry Key
     *
     * @return bool Is Removing Data From Redis Successfully
     */
    public function remove(?string $key = null): bool
    {
        if (empty($key)) {
            return false;
        }

        $key = $this->_getKey($key);

        return (bool) $this->_client->del($key);
    }

    /**
     * Sending Message To Redis Channel
     *
     * @param string|null $channel Redis Channel
     * @param string|null $message Message Data
     */
    public function sendToChannel(
        ?string $channel = null,
        ?string $message = null
    ): void
    {
        if (!empty($channel) && !empty($message)) {
            $this->_client->publish($channel, $message);
        }
    }

    /**
     * Removing Data From Redis By Pattern
     *
     * @param string|null $keyPattern Redis Entries Key Pattern
     *
     * @return bool Is Removing Data From Redis Successfully
     */
    public function removeByPattern(?string $keyPattern = null): bool
    {
        if (empty($keyPattern)) {
            return false;
        }

        $keyPattern = $this->_getKey($keyPattern);

        $keys = (array) $this->_client->keys($keyPattern);

        if (empty($keys)) {
            return false;
        }

        array_map([$this->_client, 'del'], $keys);

        return true;
    }
}
