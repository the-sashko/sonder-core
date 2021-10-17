<?php

namespace Sonder\Plugins\Sms\Interfaces;

interface ISmsResponse
{
    /**
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string;

    /**
     * @return string|null
     */
    public function getRemoteMessageCode(): ?string;
}
