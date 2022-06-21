<?php

namespace Sonder\Plugins\Database\Interfaces;

interface IDataBaseCredentials
{
    /**
     * @return string
     */
    public function getDsn(): string;

    /**
     * @return string|null
     */
    public function getUser(): ?string;

    /**
     * @return string|null
     */
    public function getPassword(): ?string;

    /**
     * @return string|null
     */
    public function getCacheType(): ?string;
}
