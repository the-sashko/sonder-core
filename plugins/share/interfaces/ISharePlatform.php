<?php

namespace Sonder\Plugins\Share\Interfaces;

interface ISharePlatform
{
    /**
     * @param string $message
     *
     * @return bool
     */
    public function send(string $message): bool;
}
