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
use Jose\Component\Core\JWK;
use Jose\Component\Encryption\Algorithm\KeyEncryption\Dir;

/**
 * final class DirAlgorithmTest.
 *
 * @group Unit
 */
final class DirAlgorithmTest extends AbstractEncryptionTest
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Wrong key type.
     */
    public function testInvalidKey()
    {
        $key = JWK::create([
            'kty' => 'EC',
        ]);

        $dir = new Dir();

        $dir->getCEK($key);
    }

    public function testValidCEK()
    {
        $key = JWK::create([
            'kty' => 'oct',
            'k' => Base64Url::encode('ABCD'),
        ]);

        $dir = new Dir();

        $this->assertEquals('ABCD', $dir->getCEK($key));
    }
}