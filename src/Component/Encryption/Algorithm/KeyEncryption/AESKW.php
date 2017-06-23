<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2017 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Component\Encryption\Algorithm\KeyEncryption;

use Assert\Assertion;
use Base64Url\Base64Url;
use Jose\Component\Core\JWK;

/**
 * Class AESKW.
 */
abstract class AESKW implements KeyWrappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function wrapKey(JWK $key, string $cek, array $complete_headers, array &$additional_headers): string
    {
        $this->checkKey($key);
        $wrapper = $this->getWrapper();

        return $wrapper->wrap(Base64Url::decode($key->get('k')), $cek);
    }

    /**
     * {@inheritdoc}
     */
    public function unwrapKey(JWK $key, string $encrypted_cek, array $complete_headers): string
    {
        $this->checkKey($key);
        $wrapper = $this->getWrapper();

        return $wrapper->unwrap(Base64Url::decode($key->get('k')), $encrypted_cek);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyManagementMode(): string
    {
        return self::MODE_WRAP;
    }

    /**
     * @param JWK $key
     */
    protected function checkKey(JWK $key)
    {
        Assertion::eq($key->get('kty'), 'oct', 'Wrong key type.');
        Assertion::true($key->has('k'), 'The key parameter "k" is missing.');
        Assertion::eq($this->getKeySize(), mb_strlen(Base64Url::decode($key->get('k')), '8bit'), 'The key size is not valid');
    }

    /**
     * @return int
     */
    abstract protected function getKeySize(): int;

    /**
     * @return \AESKW\A128KW|\AESKW\A192KW|\AESKW\A256KW
     */
    abstract protected function getWrapper();
}