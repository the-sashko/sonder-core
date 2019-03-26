<?php
class RedisPlugin
{
    public $client = NULL;
    public $ttl = -1;
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

    private function _getRedisConfig() : array
    {
        $configPath = $this->_getConfigPath();

        if (!file_exists($configPath) || !is_file($configPath)) {
            throw new Exception('Redis Config Not Found');
        }

        $settings = file_get_contents($configPath);

        return (array) json_decode($settings, true);
    }

    private function _getConfigPath() : string
    {
        return realpath(__DIR__.'/../../../config/redis.json');
    }

    public function getTTL(int $ttl = -1) : int
    {
        if ($ttl > 0) {
            return $ttl;
        }

        return $this->ttl;
    }

    public function setTTL(int $ttl = -1) : void
    {
        if ($ttl > 0) {
            $this->ttl = $ttl;
        }
    }


    public function getKey(string $key = '') : string
    {
        if (strlen($this->keyPrefix) > 0) {
            return $this->keyPrefix.':'.$key;
        }

        return $key;
    }

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

    public function set(
        string $key = '',
        string $value = '',
        int $ttl = -1
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

    public function get(string $key = '') : string
    {
        $key = $this->getKey($key);

        return (string) $this->client->get($key);
    }

    public function del(string $key = '') : bool
    {
        $key = $this->getKey($key);

        return (bool) $this->client->del($key);
    }

    public function sendToChannel(
        string $channel = '',
        string $message = ''
    ) : void
    {
        if (strlen($channel) > 0 && strlen($message) > 0) {
            $this->client->publish($channel, $message);
        }
    }

    public function delByPattern(string $keyPattern = '') : bool
    {
        if(!strlen($keyPattern) > 0) {
            return false;
        }

        $keyPattern = $this->getKey($keyPattern);

        $keys = (array) $this->client->keys($keyPattern);

        if(!count($keys) > 0){
            return false;
        }

        array_map(array($this->client, 'del'), $keys);

        return true;
    }
}
?>