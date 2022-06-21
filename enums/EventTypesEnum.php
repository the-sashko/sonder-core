<?php

namespace Sonder\Enums;

use Sonder\Core\Interfaces\ICoreEnum;
use Sonder\Interfaces\IEventTypesEnum;

#[ICoreEnum]
#[IEventTypesEnum]
enum EventTypesEnum: string implements IEventTypesEnum
{
case APP_RUN = 'app_run';
case BEFORE_MIDDLEWARES = 'before_middlewares';
case AFTER_MIDDLEWARES = 'after_middlewares';
case INIT_CONTROLLER = 'init_controller';
case BEFORE_RENDER = 'before_render';
case AFTER_RENDER = 'after_render';
    }
