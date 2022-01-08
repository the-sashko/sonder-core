<?php

namespace Sonder\Models\Role;

use Sonder\Core\ValuesObject;
use Sonder\Core\Interfaces\IRoleValuesObject;

final class RoleValuesObject extends ValuesObject implements IRoleValuesObject
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
    }

    public function can(string $roleActionIdent): bool
    {
        //TODO

        return true;
    }

    //TODO: cdate, mdate, ddate
}
