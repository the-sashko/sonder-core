<?php

namespace Sonder\Model\Config\Exceptions;

use Attribute;
use Sonder\Core\Interfaces\ICoreException;
use Sonder\Models\Config\Interfaces\IConfigException;

#[ICoreException]
#[IConfigException]
#[Attribute(Attribute::TARGET_CLASS)]
class ConfigValuesObjectException
    extends ConfigException
    implements IConfigException
{
    final public const MESSAGE_VALUES_OBJECT_METHOD_IS_PROHIBITED = 'Method "%s" is prohibited for this class';
}
