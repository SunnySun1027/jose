<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2017 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Component\Core\Util\Ecc\Random;

use Jose\Component\Core\Util\Ecc\Math\GmpMath;

final class RandomNumberGenerator
{
    /**
     * @var GmpMath
     */
    private $adapter;

    /**
     * RandomNumberGenerator constructor.
     */
    public function __construct()
    {
        $this->adapter = new GmpMath();
    }
}
