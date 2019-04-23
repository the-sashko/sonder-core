<?php
/**
 * Plugin For Cryptographic Functions
 */
class CryptPlugin
{
    /**
     * Get Hash From Input String And Salt
     *
     * @param string $input Input String Value
     * @param string $salt  Salt Value
     *
     * @return string Hash
     */
    public function getHash(
        string $input = '',
        string $salt = ''
    ) : string {
        $md5Hash    = hash('md5', $input);
        $sha256Hash = hash('sha256', strlen($salt).$input.$salt);

        return hash('sha512', strlen($input).$sha256Hash.$input.$md5Hash);
    }

    /**
     * Get Trip Code From Input String
     *
     * @param string $input Input String Value
     *
     * @return string Trip Code
     */
    public function getTripCode(string $input = '') : string
    {
        if (!strlen($input)>0) {
            return '';
        }

        $salt = substr($input."H.", 1, 2);
        $salt = preg_replace('/[^\.-z]/su', '.', $salt);
        $salt = str_replace(
            [
                ':', ';', '<',
                '=', '>', '?',
                '@', '[', "\\",
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

        return '!'.substr(crypt($input, $salt), -10);
    }
}
?>
