<?php

namespace Sonder\Plugins\Sms\Interfaces;

interface ISmsPlatform
{
    /**
     * @param string|null $phone
     * @param string|null $message
     *
     * @return ISmsResponse|null
     */
    public function sendMessage(
        ?string $phone = null,
        ?string $message = null
    ): ?ISmsResponse;

    /**
     * @param IsmSResponse|null $smsResponse
     *
     * @return bool
     */
    public function checkMessage(?IsmSResponse $smsResponse = null): bool;
}
