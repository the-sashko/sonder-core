<?php

namespace Sonder\Models\Cron\Interfaces;

use Attribute;
use Sonder\Core\Interfaces\ICoreEnum;

#[ICoreEnum]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICronStatusesEnum extends ICoreEnum
{
}
