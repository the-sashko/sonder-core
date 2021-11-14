<?php

namespace Sonder\Models\User;

use Sonder\Core\ValuesObject;
use Sonder\Models\Role;

final class UserVO extends ValuesObject
{
    final public function getId(): int
    {
        //TODO

        return 0;
    }

    final public function getLogin(): string
    {
        //TODO

        return '';
    }

    final public function getRole(): ?Role
    {
        //TODO

        return null;
    }

    final public function getPasswordHash(): ?string
    {
        //TODO

        return null;
    }

    final public function getAuthToken(): ?string
    {
        //TODO

        return null;
    }

    final public function getSessionToken(): ?string
    {
        //TODO

        return null;
    }

    final public function setId(?int $id = null): void
    {
        //TODO
    }

    final public function setLogin(?string $login = null): void
    {
        //TODO
    }

    final public function setRole(?Role $role = null): void
    {
        //TODO
    }

    final public function setPasswordHash(?string $passwordHash = null): void
    {
        //TODO
    }

    final public function setAuthToken(?string $authToken = null): void
    {
        //TODO
    }

    final public function setSessionToken(?string $sessionToken = null): void
    {
        //TODO
    }

    //TODO: cdate, mdate, ddate
}
