<?php

namespace Sonder\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Interfaces\ICacheTypesEnum;

#[ICoreEnum]
#[ICacheTypesEnum]
enum CacheTypesEnum: string implements ICacheTypesEnum
{
case APP = 'app';
case CONFIG = 'config';

    final public const DEFAULT = CacheTypesEnum::APP;
    }
