<?php

namespace Sonder\Plugins\Share\Platforms;

abstract class AbstractPlatform
{
    /**
     * @var array
     */
    protected array $credentials;

    /**
     * @param array $credentials
     */
    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @param string $message
     *
     * @return bool
     */
    abstract public function send(string $message): bool;
}
