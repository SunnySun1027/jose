<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2017 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Component\Encryption\Tests;

use Base64Url\Base64Url;
use Jose\Component\Core\JWKSet;
use Jose\Component\Encryption\JWE;

/**
 * final class FlattenedTest.
 *
 * @group Functional
 */
final class JWEFlattenedTest extends AbstractEncryptionTest
{
    /**
     * @see https://tools.ietf.org/html/rfc7516#appendix-A.5
     */
    public function testLoadFlattenedJWE()
    {
        $jweLoader = $this->getJWELoaderFactory()->create(['A128KW'], ['A128CBC-HS256'], ['DEF'], [], ['jwe_compact', 'jwe_json_flattened', 'jwe_json_general']);

        $loaded = $jweLoader->load('{"protected":"eyJlbmMiOiJBMTI4Q0JDLUhTMjU2In0","unprotected":{"jku":"https://server.example.com/keys.jwks"},"header":{"alg":"A128KW","kid":"7"},"encrypted_key":"6KB707dM9YTIgHtLvtgWQ8mKwboJW3of9locizkDTHzBC2IlrT1oOQ","iv":"AxY8DCtDaGlsbGljb3RoZQ","ciphertext":"KDlTtXchhZTGufMYmOYGS4HffxPSUrfmqCHXaI9wOGY","tag":"Mz-VPPyU4RlcuYv1IwIvzw"}');

        self::assertInstanceOf(JWE::class, $loaded);
        self::assertEquals('A128KW', $loaded->getRecipient(0)->getHeader('alg'));
        self::assertEquals('A128CBC-HS256', $loaded->getSharedProtectedHeader('enc'));
        self::assertNull($loaded->getPayload());

        $loaded = $jweLoader->decryptUsingKeySet($loaded, $this->getSymmetricKeySet(), $index);

        self::assertEquals(0, $index);
        self::assertEquals('Live long and prosper.', $loaded->getPayload());
    }

    /**
     * @return JWKSet
     */
    private function getSymmetricKeySet(): JWKSet
    {
        $keys = ['keys' => [
            [
                'kid' => 'DIR_1',
                'kty' => 'oct',
                'k' => Base64Url::encode(hex2bin('00112233445566778899AABBCCDDEEFF000102030405060708090A0B0C0D0E0F')),
            ],
            [
                'kty' => 'oct',
                'k' => 'f5aN5V6iihwQVqP-tPNNtkIJNCwUb9-JukCIKkF0rNfxqxA771RJynYAT2xtzAP0MYaR7U5fMP_wvbRQq5l38Q',
            ],
            [
                'kty' => 'oct',
                'k' => 'GawgguFyGrWKav7AX4VKUg',
            ],
            [
                'kty' => 'oct',
                'k' => 'AyM1SysPpbyDfgZld3umj1qzKObwVMkoqQ-EstJQLr_T-1qS0gZH75aKtMN3Yj0iPS4hcgUuTwjAzZr1Z9CAow',
            ],
        ]];

        return JWKSet::createFromKeyData($keys);
    }
}
