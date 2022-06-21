<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleModel
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
