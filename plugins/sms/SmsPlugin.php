<?php

namespace Sonder\Plugins;

use Exception;
use Sonder\Plugins\Sms\Interfaces\ISmsPlatform;

final class SmsPlugin
{
    const SMS_TEXT_MAX_LENGTH = 70;

    /**
     * @var array
     */
    private array $_configValues;

    /**
     * @var ISmsPlatform
     */
    private ISmsPlatform $_platform;

    /**
     * @param array $configValues
     */
    final public function __construct(array $configValues)
    {
        $this->_configValues = $configValues;
    }

    /**
     * @param string|null $platformIdent
     *
     * @throws Exception
     */
    final public function setPlatform(?string $platformIdent = null): void
    {
        if (empty($platformIdent)) {
            throw new Exception('SMS Plugin Platform Is Not Set');
        }

        $platformClass = sprintf(
            'Sonder\Plugins\Sms\Platforms\%sPlatform',
            $platformIdent
        );

        if (!class_exists($platformClass)) {
            throw new Exception('Invalid SMS Plugin Platform');
        }

        $this->_platform = new $platformClass($this->_configValues);
    }

    /**
     * @param string|null $phone
     * @param string|null $message
     *
     * @return array
     *
     * @throws Exception
     */
    final public function sendMessage(
        ?string $phone = null,
        ?string $message = null
    ): array
    {
        if (empty($phone)) {
            throw new Exception('SMS Phone Is Not Set');
        }

        if (empty($message)) {
            throw new Exception('SMS Message Is Not Set');
        }

        if (empty($this->_platform)) {
            throw new Exception('SMS Platform Is Not Set');
        }

        if (!$this->_validatePhone($phone)) {
            return [false, 'Invalid Phone Number'];
        }

        if (strlen($message) > SmsPlugin::SMS_TEXT_MAX_LENGTH) {
            return [false, 'SMS Text Is Too Long'];
        }

        $response = $this->_platform->sendMessage($phone, $message);

        if (!$response->getStatus()) {
            return [false, $response->getErrorMessage()];
        }

        return [true, $response->getRemoteMessageCode()];
    }

    /**
     * @param string|null $phone
     *
     * @return bool
     */
    private function _validatePhone(?string $phone = null): bool
    {
        if (empty($phone)) {
            return false;
        }

        return preg_match('/^\+([0-9]+)$/su', $phone);
    }
}
