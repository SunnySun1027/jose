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

namespace Jose\Component\Signature\Algorithm;

/**
 * This class handles signatures using HMAC.
 * It supports HS512;.
 *
 * Class HS512
 */
final class HS512 extends HMAC
{
    /**
     * @return string
     */
    protected function getHashAlgorithm(): string
    {
        return 'sha512';
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'HS512';
    }
}