<?php

namespace Sonder\Plugins\Share\Interfaces;

interface ISharePlugin
{
    /**
     * @param string|null $message
     *
     * @return bool
     */
    public function send(?string $message = null): bool;
}
