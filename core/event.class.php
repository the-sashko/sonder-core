<?php

namespace Sonder\Core;

use Sonder\Core\Interfaces\IEvent;
use Sonder\Core\Interfaces\IHook;

final class CoreEvent implements IEvent
{
    const TYPE_BEFORE_MIDDLEWARES = 'before_middlewares';
    const TYPE_AFTER_MIDDLEWARES = 'after_middlewares';
    const TYPE_BEFORE_INIT_CONTROLLER = 'before_init_controller';
    const TYPE_AFTER_INIT_CONTROLLER = 'after_init_controller';
    const TYPE_BEFORE_RENDER = 'before_render';
    const TYPE_AFTER_RENDER = 'after_render';

    private array $_hooks = [];

    final public function run(string $type): void
    {
        //TODO
    }

    final public function subscribe(string $type, IHook $hook): void
    {
        //TODO
    }
}
