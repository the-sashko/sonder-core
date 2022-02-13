<?php

namespace Sonder\Core\Interfaces;

interface ICron
{
    /**
     * @return array|null
     */
    public function getJobsForRunning(): ?array;
}
