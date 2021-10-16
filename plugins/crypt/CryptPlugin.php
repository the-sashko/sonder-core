<?php

namespace Sonder\Plugins;

final class CryptPlugin
{
    /**
     * @param string|null $input
     * @param string|null $salt
     *
     * @return string|null
     */
    final public function getHash(
        ?string $input = null,
        ?string $salt = null
    ): ?string
    {
        if (empty($input)) {
            return null;
        }

        if (empty($salt)) {
            return null;
        }

        $md5Hash = hash('md5', $input);
        $sha256Hash = hash('sha256', strlen($salt) . $input . $salt);

        return hash('sha512', strlen($input) . $sha256Hash . $input . $md5Hash);
    }

    /**
     * @param string|null $input
     *
     * @return string|null
     */
    final public function getTripCode(?string $input = null): ?string
    {
        if (empty($input)) {
            return null;
        }

        $salt = sprintf('%sH.', $input);
        $salt = substr($salt, 1, 2);
        $salt = preg_replace('/[^.-z]/su', '.', $salt);

        $salt = str_replace(
            [
                ':', ';', '<',
                '=', '>', '?',
                '@', '[', '\\',
                ']', '^', '_',
                '`'
            ],
            [
                'A', 'B', 'C',
                'D', 'E', 'F',
                'G', 'a', 'b',
                'c', 'd', 'e',
                'f'
            ],
            $salt
        );

        return sprintf('!%s', substr(crypt($input, $salt), -10));
    }
}
