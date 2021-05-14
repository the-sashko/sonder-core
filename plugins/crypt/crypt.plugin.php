<?php
/**
 * Plugin For Cryptographic Functions
 */
class CryptPlugin
{
    /**
     * Get Hash From Input String And Salt
     *
     * @param string|null $input Input String Value
     * @param string|null $salt  Salt Value
     *
     * @return string|null Hash
     */
    public function getHash(
        ?string $input = null,
        ?string $salt  = null
    ): ?string
    {
        if (empty($input)) {
            return null;
        }

        if (empty($salt)) {
            return null;
        }

        $md5Hash    = hash('md5', $input);
        $sha256Hash = hash('sha256', strlen($salt).$input.$salt);

        return hash('sha512', strlen($input).$sha256Hash.$input.$md5Hash);
    }

    /**
     * Get Trip Code From Input String
     *
     * @param string|null $input Input String Value
     *
     * @return string|null Trip Code
     */
    public function getTripCode(?string $input = null): ?string
    {
        if (empty($input)) {
            return null;
        }

        $salt = sprintf('%sH.', $input);
        $salt = substr($salt, 1, 2);
        $salt = preg_replace('/[^\.-z]/su', '.', $salt);

        $salt = str_replace(
            [
                ':', ';', '<',
                '=', '>', '?',
                '@', '[', '\\',
                ']', '^', '_',
                '`'
            ],
            [
                'A','B','C',
                'D','E','F',
                'G','a','b',
                'c','d','e',
                'f'
            ],
            $salt
        );

        return sprintf('!%s', substr(crypt($input, $salt), -10));
    }
}
