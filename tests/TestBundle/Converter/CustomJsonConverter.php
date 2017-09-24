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

namespace Jose\Test\TestBundle\Converter;

use Jose\Component\Core\Converter\JsonConverterInterface;

/**
 * Class CustomJsonConverter.
 */
final class CustomJsonConverter implements JsonConverterInterface
{
    /**
     * @var int
     */
    private $options;

    /**
     * CustomJsonConverter constructor.
     */
    public function __construct()
    {
        $this->options = JSON_UNESCAPED_UNICODE;
    }

    /**
     * {@inheritdoc}
     */
    public function encode($payload): string
    {
        return json_encode($payload, $this->options, 512);
    }

    /**
     * {@inheritdoc}
     */
    public function decode(string $payload)
    {
        return json_decode($payload, true, 512, $this->options);
    }
}
