<?php

namespace Sonder\Interfaces;

use Attribute;

#[IModel]
#[Attribute(Attribute::TARGET_CLASS)]
interface ICronModel extends IModel
{
    /**
     * @param ICronValuesObject|null $cronVO
     * @return bool
     */
    public function runJob(?ICronValuesObject $cronVO = null): bool;

    /**
     * @return array|null
     */
    public function getJobsForRunning(): ?array;
}
