<?php

namespace Sonder\Models;

use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\Interfaces\IRole;

class Role extends CoreModel implements IModel, IRole
{
    /**
     * @param string $roleActionIdent
     *
     * @return bool
     */
    public function can(string $roleActionIdent): bool
    {
        //TODO

        return false;
    }

    //TODO
}
