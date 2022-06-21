<?php

namespace Sonder\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Interfaces\IApiResponseStatusesEnum;

#[ICoreEnum]
#[IApiResponseStatusesEnum]
enum ApiResponseStatusesEnum: string implements IApiResponseStatusesEnum
{
case SUCCESS = 'success';
case ERROR = 'error';
    }
