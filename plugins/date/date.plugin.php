<?php

use Core\Plugins\Date\Interfaces\IDatePlugin;

class DatePlugin implements IDatePlugin
{
    const MONTHS = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];

    /**
     * @throws LanguageException
     */
    public function getDateFromTimestamp(?int $timestamp = null): ?string
    {
        if (empty($timestamp)) {
            return null;
        }

        $year = (int)date('Y', $timestamp);
        $month = (int)date('m', $timestamp);
        $day = (int)date('d', $timestamp);

        return sprintf(
            '%d %s %d',
            $day,
            __t(static::MONTHS[$month - 1]),
            $year
        );
    }
}