<?php

namespace Sonder\Plugins\Sms\Platforms;

use Sonder\Plugins\Sms\Interfaces\ISmsCredentials;

final class SmsClubCredentials implements ISmsCredentials
{
    /**
     * @var array
     */
    private array $_values;

    /**
     * @param array $values
     */
    final public function __construct(array $values)
    {
        $this->_values = $values;
    }

    /**
     * @return string|null
     */
    final public function getLogin(): ?string
    {
        if (!array_key_exists('login', $this->_values)) {
            return null;
        }

        return (string) $this->_values['login'];
    }

    /**
     * @return string|null
     */
    final public function getToken(): ?string
    {
        if (!array_key_exists('token', $this->_values)) {
            return null;
        }

        return (string) $this->_values['token'];
    }

    /**
     * @return string|null
     */
    final public function getUrl(): ?string
    {
        if (!array_key_exists('url', $this->_values)) {
            return null;
        }

        return (string) $this->_values['url'];
    }

    /**
     * @return string|null
     */
    final public function getAlphaName(): ?string
    {
        if (!array_key_exists('alpha_name', $this->_values)) {
            return null;
        }

        return (string) $this->_values['alpha_name'];
    }

    /**
     * @return array|null
     */
    final public function getOptions(): ?array
    {
        if (!array_key_exists('options', $this->_values)) {
            return null;
        }

        return (array) $this->_values['options'];
    }
}
