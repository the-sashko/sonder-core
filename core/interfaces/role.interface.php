<?php

namespace Sonder\Core\Interfaces;

interface IRole
{
    /**
     * @param int|null $id
     * @return IRoleValuesObject|null
     */
    public function getVOById(?int $id = null): ?IRoleValuesObject;

    /**
     * @return IRoleValuesObject
     */
    public function getGuestVO(): IRoleValuesObject;
}
