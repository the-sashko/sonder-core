<?php

namespace Sonder\Models\Role\Interfaces;

use Attribute;
use Sonder\Interfaces\IModelSimpleValuesObject;

#[IModelSimpleValuesObject]
#[Attribute(Attribute::TARGET_CLASS)]
interface IRoleActionSimpleValuesObject extends IModelSimpleValuesObject
{
    /**
     * @return string
     */
    public function getName(): string;
}
