<?php

namespace Sonder\Plugins;

final class MathPlugin
{
    /**
     * @param int $decimal
     * @param int $base
     *
     * @return string
     */
    final public function dec2base64(int $decimal = 0, int $base = 64): string
    {
        $digitalAlphabet = array(
            '0', '1', '2', '3', '4', '5', '6', '7',
            '8', '9', 'a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
            'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y', 'Z', '_', '-'
        );

        $result = '';

        if ($decimal < 1) {
            return '0';
        }

        if ($base < 2) {
            $base = 2;
        }

        if ($base > 64) {
            $base = 64;
        }

        while ($decimal > 0) {
            $remainder = $decimal % $base;
            $decimal = ($decimal - $remainder) / $base;
            $result = $digitalAlphabet[$remainder] . $result;
        }

        return $result;
    }
}
