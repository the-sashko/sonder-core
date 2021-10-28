<?php

namespace Sonder\Core\Interfaces;

interface IEvent
{
    public function run(string $type): void;

    public function subscribe(string $type, IHook $hook): void;
}
