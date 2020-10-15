<?php
namespace Core\Plugins\Database\Interfaces;

interface IDataBaseCredentials
{
    public function getDsn(): string;

    public function getUser(): ?string;

    public function getPassword(): ?string;

    public function getCacheType(): ?string;
}
