<?php

namespace Sonder\Core;

use Sonder\Interfaces\IHook;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\HookException;

#[IHook]
class CoreHook extends CoreObject implements IHook
{
    /**
     * @param array $_values
     */
    final public function __construct(private array $_values)
    {
        parent::__construct();
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
     * @throws HookException
     */
    final protected function get(string $valueName): mixed
    {
        if (!$this->has($valueName)) {
            $errorMessage = sprintf(
                HookException::MESSAGE_HOOK_VALUE_NOT_EXISTS,
                $valueName
            );

            throw new HookException(
                $errorMessage,
                AppException::CODE_HOOK_VALUE_NOT_EXISTS
            );
        }

        return $this->_values[$valueName];
    }

    /**
     * @param string $valueName
     * @param mixed|null $value
     * @return void
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
