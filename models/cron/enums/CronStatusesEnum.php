<?php

namespace Sonder\Models\Cron\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Models\Cron\Interfaces\ICronStatusesEnum;

#[ICoreEnum]
#[ICronStatusesEnum]
enum CronStatusesEnum: string implements ICronStatusesEnum
{
    case SCHEDULED = 'scheduled';
    case RUNNING = 'running';
    case ERROR = 'error';
    case UNKNOWN = 'unknown';

    final public const DEFAULT = CronStatusesEnum::UNKNOWN;
}
