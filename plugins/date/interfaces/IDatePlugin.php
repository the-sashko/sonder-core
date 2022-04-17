<?php
namespace Sonder\Plugins\Date\Interfaces;

interface IDatePlugin
{
    /**
     * @param int|null $timestamp
     *
     * @return string|null
     */
    public function getDateFromTimestamp(?int $timestamp = null): ?string;
}
