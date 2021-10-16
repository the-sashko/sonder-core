<?php

namespace Sonder\Plugins;

use Sonder\Plugins\Date\Interfaces\IDatePlugin;

final class DatePlugin implements IDatePlugin
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
     * @param int|null $timestamp
     *
     * @return string|null
     *
     * @throws Language\Exceptions\LanguageException
     */
    final public function getDateFromTimestamp(?int $timestamp = null): ?string
    {
        if (empty($timestamp)) {
            return null;
        }

        $year = (int)date('Y', $timestamp);
        $month = (int)date('m', $timestamp);
        $day = (int)date('d', $timestamp);

        $month = DatePlugin::MONTHS[$month - 1];

        if (function_exists('__t')) {
            $month = __t($month);
        }

        return sprintf('%d %s %d', $day, $month, $year);
    }
}
