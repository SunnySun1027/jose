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

namespace Jose\Component\KeyManagement\KeyAnalyzer;

use Jose\Component\Core\JWK;

/**
 * Interface JWKAnalyzerInterface.
 */
interface JWKAnalyzerInterface
{
    /**
     * @param JWK      $jwk
     * @param string[] $messages
     */
    public function analyze(JWK $jwk, array &$messages);
}
