<?php

namespace Sonder\Model\Config\Exceptions;

use Attribute;
use Sonder\Exceptions\BaseException;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Config\Interfaces\IConfigException;

#[ICoreException]
#[IConfigException]
#[Attribute(Attribute::TARGET_CLASS)]
class ConfigException extends BaseException implements IConfigException
{
    final public const CODE_VALUES_OBJECT_METHOD_IS_PROHIBITED = 1001;

    final public const CODE_MODEL_METHOD_CONST_IS_NOT_DEFINED = 2001;
}
