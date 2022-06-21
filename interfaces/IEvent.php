<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
interface IEvent
{
    /**
     * @param IEventTypesEnum $type
     * @param array $values
     * @return array
     */
    public function run(IEventTypesEnum $type, array $values): array;
}
