<?php

use PHPUnit\Framework\TestCase;

/**
 * Class For Testing CryptPlugin Class Methods
 */
class CryptPluginTest extends TestCase
{
    /**
     * @var array Data Sample For Unit Tests
     */
    const HASH_DATA_SAMPLE = [
        [
            'value' => 'foo',
            'expected' => [
                'hash' => 'ba32cfa7e6128ab77ddf59736fbd9a5153aad9c42e08a' .
                    '2a756f6cc86493310b7488457503b121b428529c27bfb' .
                    '9a1acd8abcf69e5ee0b38435a19210bc5d6802',
                'trip_code' => '!2bgybBZ7HI'
            ]
        ],
        [
            'value' => 'bar',
            'expected' => [
                'hash' => '7cb550d9a29de74e252baab089075bda4a1b333e0d9d2' .
                    '7babeb72ddc8654cdf37723440219e2f8fb65a25770b4' .
                    '2ab22bf071b450253f982efb41820753c7f433',
                'trip_code' => '!X9qycma/WI'
            ]
        ],
        [
            'value' => '',
            'expected' => [
                'hash' => '',
                'trip_code' => ''
            ]
        ]
    ];

    /**
     * @var array Data Sample SALT For Unit Tests
     */
    const SALT = 'foo_bar';

    /**
     * Unit Test Of getHash Method
     *
     * @throws CoreException
     */
    final public function testGetHash()
    {
        $crypt = (new CommonCore)->getPlugin('crypt');

        foreach (static::HASH_DATA_SAMPLE as $data) {
            $hash = $crypt->getHash($data['value'], static::SALT);
            $this->assertEquals($data['expected']['hash'], $hash);
        }
    }

    /**
     * Unit Test Of getTripCode Method
     *
     * @throws CoreException
     */
    final public function testGetTripCode()
    {
        $crypt = (new CommonCore)->getPlugin('crypt');

        foreach (static::HASH_DATA_SAMPLE as $data) {
            $tripCode = $crypt->getTripCode($data['value']);
            $this->assertEquals($data['expected']['trip_code'], $tripCode);
        }
    }
}
