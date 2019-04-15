<?php
/**
 * Plugin For Working With Redis
 */
class RedisPlugin
{
    /**
     * @var object Predis Client Instance
     */
    public $client = NULL;

    /**
     * @var int Default Time To Live Redis Data
     */
    public $ttl = -1;

    /**
     * @var string Redis Entry Key Prefix
     */
    public $keyPrefix = '';

    public function __construct()
    {
        $settings = $this->_getSettings();

        Predis\Autoloader::register();

        $this->client = new Predis\Client($settings);
    }

    public function __destruct()
    {
        $this->client->disconnect();
    }

    /**
     * Get Redis Settings From Config File
     *
     * @return array Redis Settings Data
     */
    private function _getSettings() : array
    {
        $settings = $this->_getRedisConfig();

        if (!$this->_validateSettings($settings)) {
            throw new Exception('Redis Config Has Bad Format');
        }

        if (array_key_exists('key_prefix', $settings)) {
            $this->keyPrefix = $settings['key_prefix'];
            unset($settings['key_prefix']);
        }

        return $settings;
    }

    /**
     * Check Is Redis Settings Data Has Valid Format
     *
     * @param array $settings Redis Settings Data
     *
     * @return bool Is Redis Settings Data Has Valid Format
     */
    private function _validateSettings(array $settings = []) : bool
    {
        if (count($settings) < 4) {
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
    private function _getRedisConfig() : array
    {
        $configPath = $this->_getConfigPath();

        if (!file_exists($configPath) || !is_file($configPath)) {
            throw new Exception('Redis Config Not Found');
        }

        $settings = file_get_contents($configPath);

        return (array) json_decode($settings, true);
    }

    /**
     * Get Redis Config File Path
     *
     * @return string Redis Config File Path
     */
    private function _getConfigPath() : string
    {
        return realpath(__DIR__.'/../../../config/redis.json');
    }

    /**
     * Get Redis Data Time To Live
     *
     * @return int Redis Data Time To Live
     */
    public function getTTL(int $ttl = -1) : int
    {
        if ($ttl > 0) {
            return $ttl;
        }

        return $this->ttl;
    }

    /**
     * Set Redis Data Time To Live
     *
     * @param int Redis Data Time To Live
     */
    public function setTTL(int $ttl = -1) : void
    {
        if ($ttl > 0) {
            $this->ttl = $ttl;
        }
    }

    /**
     * Get Redis Entry Key
     *
     * @param string $key Input Redis Entry Key Value
     *
     * @return string Output Redis Entry Key Value
     */
    public function getKey(string $key = '') : string
    {
        if (strlen($this->keyPrefix) > 0) {
            return $this->keyPrefix.':'.$key;
        }

        return $key;
    }

    /**
     * Set Redis Entry Key Prefix
     *
     * @param string $keyPrefix Redis Entry Key Prefix
     */
    public function setKeyPrefix(string $keyPrefix = '') : void
    {
        if (strlen($keyPrefix) > 0) {
            if (strlen($this->keyPrefix) > 0) {
                $this->keyPrefix = $this->keyPrefix.':'.$keyPrefix;
            } else {
                $this->keyPrefix = $this->keyPrefix.':'.$keyPrefix;
            }
        }
    }

    /**
     * Saving Data To Redis
     *
     * @param string $key   Redis Entry Key
     * @param string $value Redis Entry Value
     * @param int    $ttl   Redis Entry Time To Live
     *
     * @return bool Is Saving Value To Redis Successfull
     */
    public function set(
        string $key   = '',
        string $value = '',
        int    $ttl   = -1
    ) : bool
    {
        $key = $this->getKey($key);
        $ttl = $this->getTTL($ttl);

        if (!strlen($key) > 0) {
            return false;
        }

        if ($ttl > 0) {
            $this->client->set($key, $value, 'EX', $ttl);
        } else {
            $this->client->set($key, $value);
        }

        return true;
    }

    /**
     * Get Data From Redis
     *
     * @param string $key Redis Entry Key
     *
     * @return string Redis Entry Value
     */
    public function get(string $key = '') : string
    {
        $key = $this->getKey($key);

        return (string) $this->client->get($key);
    }

    /**
     * Removing Data From Redis By Key
     *
     * @param string $key Redis Entry Key
     *
     * @return bool Is Removing Data From Redis Successfull
     */
    public function del(string $key = '') : bool
    {
        $key = $this->getKey($key);

        return (bool) $this->client->del($key);
    }

    /**
     * Sending Message To Redis Channel
     *
     * @param string $channel Redis Channel
     * @param string $message Message Data
     */
    public function sendToChannel(
        string $channel = '',
        string $message = ''
    ) : void
    {
        if (strlen($channel) > 0 && strlen($message) > 0) {
            $this->client->publish($channel, $message);
        }
    }

    /**
     * Removing Data From Redis By Pattern
     *
     * @param string $keyPattern Redis Entries Key Pattern
     *
     * @return bool Is Removing Data From Redis Successfull
     */
    public function delByPattern(string $keyPattern = '') : bool
    {
        if (!strlen($keyPattern) > 0) {
            return false;
        }

        $keyPattern = $this->getKey($keyPattern);

        $keys = (array) $this->client->keys($keyPattern);

        if (!count($keys) > 0) {
            return false;
        }

        array_map(array($this->client, 'del'), $keys);

        return true;
    }
}
?>