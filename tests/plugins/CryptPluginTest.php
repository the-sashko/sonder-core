<?php
use PHPUnit\Framework\TestCase;

class CryptPluginTest extends TestCase
{
    const HASH_DATA_SAMPLE = [
        [
            'value'    => 'foo',
            'expected' => [
                'hash'     => 'ba32cfa7e6128ab77ddf59736fbd9a51'.
                              '53aad9c42e08a2a756f6cc86493310b7'.
                              '488457503b121b428529c27bfb9a1acd'.
                              '8abcf69e5ee0b38435a19210bc5d6802',
                'trip_code' => '!2bgybBZ7HI'
            ]
        ],
        [
            'value'    => 'bar',
            'expected' => [
                'hash'     => '7cb550d9a29de74e252baab089075bda'.
                              '4a1b333e0d9d27babeb72ddc8654cdf3'.
                              '7723440219e2f8fb65a25770b42ab22b'.
                              'f071b450253f982efb41820753c7f433',
                'trip_code' => '!X9qycma/WI'
            ]
        ],
        [
            'value'    => '',
            'expected' => [
                'hash'     => '9b8277f7d6bb58e5a38a71c38e34ddf2'.
                              '67240b5f1a82d25b57cfd48441b1f586'.
                              '871818db9558dec9a5355f0639b7cfe3'.
                              'dcb12a3080731745d1283beae904a5fc',
                'trip_code' => ''
            ]
        ]
    ];

    const SALT = 'foo_bar';

    public function testGetHash()
    {
        $crypt = (new CommonCore)->initPlugin('crypt');

        foreach (static::HASH_DATA_SAMPLE as $data) {
            $hash = $crypt->getHash($data['value'], static::SALT);
            $this->assertEquals($data['expected']['hash'], $hash);
        }
    }

    public function testGetTripCode()
    {
        $crypt = (new CommonCore)->initPlugin('crypt');

        foreach (static::HASH_DATA_SAMPLE as $data) {
            $tripCode = $crypt->getTripCode($data['value']);
            $this->assertEquals($data['expected']['trip_code'], $tripCode);
        }
    }
}
?>