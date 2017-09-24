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

namespace Jose\Component\Console;

use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RsaKeysetGeneratorCommand.
 */
final class RsaKeysetGeneratorCommand extends AbstractGeneratorCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('keyset:generate:rsa')
            ->setDescription('Generate a key set with RSA keys (JWK format)')
            ->addArgument('size', InputArgument::REQUIRED, 'Quantity of keys in the key set.')
            ->addArgument('curve', InputArgument::REQUIRED, 'Key size.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $quantity = (int) $input->getArgument('quantity');
        $size = (int) $input->getArgument('size');
        $args = $this->getOptions($input);

        $keyset = JWKSet::createFromKeys([]);
        for ($i = 0; $i < $quantity; ++$i) {
            $keyset = $keyset->with(JWKFactory::createRSAKey($size, $args));
        }
        $this->prepareJsonOutput($input, $output, $keyset);
    }
}