<?php

namespace Sonder\Model\Config\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Config\Interfaces\IConfigException;

#[ICoreException]
#[IConfigException]
#[Attribute(Attribute::TARGET_CLASS)]
class ConfigModelException
    extends ConfigException
    implements IConfigException
{
    final public const MESSAGE_MODEL_METHOD_CONST_IS_NOT_DEFINED = 'Const "%s" is not defined';
}
