<?php

namespace Sonder\Plugins\Sms\Interfaces;

interface ISmsCredentials
{
    /**
     * @return string|null
     */
    public function getLogin(): ?string;

    /**
     * @return string|null
     */
    public function getToken(): ?string;

    /**
     * @return string|null
     */
    public function getUrl(): ?string;

    /**
     * @return string|null
     */
    public function getAlphaName(): ?string;

    /**
     * @return array|null
     */
    public function getOptions(): ?array;
}
