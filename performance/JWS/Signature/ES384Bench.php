<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2017 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Performance\JWS\Signature;

use Jose\Component\Core\JWK;
use Jose\Component\Signature\SignatureAlgorithmInterface;

/**
 * @Groups({"Signature", "ECDSA"})
 */
final class ES384Bench extends SignatureBench
{
    /**
     * @return array
     */
    public function dataVerification(): array
    {
        return [
            [
                'signature' => 'KYD8GcuF5obFaHyjMHJu-v55pfcJdTw_0DSWU1achSeVqbJsGT0wjkGqfr839ZxB5x-g7hbAHKIFzwZanWq9cxoORKgUSQC6NRhtwM-Y_21aauWhB3Zz1FrNcnpKTAIq',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAlgorithm(): SignatureAlgorithmInterface
    {
        return $this->jwaManager->get('ES384');
    }

    /**
     * {@inheritdoc}
     */
    protected function getInput(): string
    {
        return 'eyJhbGciOiJFUzM4NCJ9.SXTigJlzIGEgZGFuZ2Vyb3VzIGJ1c2luZXNzLCBGcm9kbywgZ29pbmcgb3V0IHlvdXIgZG9vci4gWW91IHN0ZXAgb250byB0aGUgcm9hZCwgYW5kIGlmIHlvdSBkb24ndCBrZWVwIHlvdXIgZmVldCwgdGhlcmXigJlzIG5vIGtub3dpbmcgd2hlcmUgeW91IG1pZ2h0IGJlIHN3ZXB0IG9mZiB0by4';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPrivateKey(): JWK
    {
        return JWK::create([
            'kty' => 'EC',
            'kid' => 'peregrin.took@tuckborough.example',
            'use' => 'sig',
            'crv' => 'P-384',
            'x' => 'YU4rRUzdmVqmRtWOs2OpDE_T5fsNIodcG8G5FWPrTPMyxpzsSOGaQLpe2FpxBmu2',
            'y' => 'A8-yxCHxkfBz3hKZfI1jUYMjUhsEveZ9THuwFjH2sCNdtksRJU7D5-SkgaFL1ETP',
            'd' => 'iTx2pk7wW-GqJkHcEkFQb2EFyYcO7RugmaW3mRrQVAOUiPommT0IdnYK2xDlZh-j',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPublicKey(): JWK
    {
        return $this->getPrivateKey()->toPublic();
    }
}
