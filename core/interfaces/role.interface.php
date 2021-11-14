<?php

namespace Sonder\Core\Interfaces;

interface IRole
{
    /**
     * @param string $roleActionIdent
     * @return bool
     */
    public function can(string $roleActionIdent): bool;
}
