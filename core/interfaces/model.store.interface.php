<?php

namespace Sonder\Core\Interfaces;

interface IModelStore
{
    public function start(): bool;

    public function commit(): bool;

    public function rollback(): bool;
}
