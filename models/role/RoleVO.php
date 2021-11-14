<?php

namespace Sonder\Models\Role;

use Sonder\Core\ValuesObject;

final class RoleVO extends ValuesObject
{
    final public function getId(): int
    {
        //TODO

        return 0;
    }

    final public function getName(): string
    {
        //TODO

        return '';
    }

    final public function getActions(): array
    {
        //TODO

        return [];
    }

    final public function setId(?int $id = null): void
    {
        //TODO
    }

    final public function setName(?string $name = null): void
    {
        //TODO
    }

    final public function setActions(?array $actions = null): void
    {
        //TODO
    }

    //TODO: cdate, mdate, ddate
}
