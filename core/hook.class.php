<?php

namespace Sonder\Core;

use Exception;
use Sonder\Core\Interfaces\IHook;

class CoreHook extends CoreObject implements IHook
{
    /**
     * @var array
     */
    private array $_values;

    /**
     * @param array $values
     * @throws Exception
     */
    final public function __construct(array $values)
    {
        parent::__construct();

        $this->_values = $values;
    }

    /**
     * @return array
     */
    final public function getValues(): array
    {
        return $this->_values;
    }

    /**
     * @param string $valueName
     * @return mixed
     * @throws Exception
     */
    final protected function get(string $valueName): mixed
    {
        if (!$this->has($valueName)) {
            $errorMessage = sprintf(
                'Value %s Not Found In Hook Values',
                $valueName
            );
            throw new Exception($errorMessage);
        }

        return $this->_values[$valueName];
    }

    /**
     * @param string $valueName
     * @param mixed|null $value
     */
    final protected function set(string $valueName, mixed $value = null): void
    {
        $this->_values[$valueName] = $value;
    }

    /**
     * @param string|null $valueName
     * @return bool
     */
    final protected function has(?string $valueName = null): bool
    {
        if (empty($valueName)) {
            return false;
        }

        return array_key_exists($valueName, $this->_values);
    }
}
