<?php

namespace Sonder\Core\Interfaces;

interface IEvent
{
    public function run(string $type, array $values): array;
}
