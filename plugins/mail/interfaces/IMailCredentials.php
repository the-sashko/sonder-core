<?php

namespace Sonder\Plugins\Mail\Interfaces;

interface IMailCredentials
{
    /**
     * @return string
     */
    public function getServerAddress(): string;

    /**
     * @return int
     */
    public function getServerPort(): int;

    /**
     * @return string
     */
    public function getLogin(): string;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @return string
     */
    public function getReplyEmail(): string;

    /**
     * @return string
     */
    public function getSenderName(): string;
}
