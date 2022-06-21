<?php

namespace Sonder\Models\Config\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Models\Config\Interfaces\IConfigProhibitedEnum;

#[ICoreEnum]
#[IConfigProhibitedEnum]
enum ConfigProhibitedEnum: string implements IConfigProhibitedEnum
{
case CRYPT = 'crypt';
case DATABASE = 'database';
case HOOKS = 'hooks';
case LOCALE = 'locale';
case SHARE = 'share';
    }
