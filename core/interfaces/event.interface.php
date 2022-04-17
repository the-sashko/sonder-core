<?php

namespace Sonder\Core\Interfaces;

interface IEvent
{
    /**
     * @param string $type
     * @param array $values
     * @return array
     */
    public function run(string $type, array $values): array;
}
