<?php
namespace Core\Plugins\Date\Interfaces;

interface IDatePlugin
{
    public function getDateFromTimestamp(?int $timestamp = null): ?string;
}
