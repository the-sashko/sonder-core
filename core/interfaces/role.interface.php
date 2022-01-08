<?php

namespace Sonder\Core\Interfaces;

use Sonder\Core\ValuesObject;

interface IRole
{
    /**
     * @param int|null $id
     * @return ValuesObject|null
     */
    public function getVOById(?int $id = null): ?ValuesObject;

    /**
     * @return ValuesObject
     */
    public function getGuestVO(): ValuesObject;
}
