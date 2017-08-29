<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Bundle\Checker;

use Jose\Bundle\Checker\DependencyInjection\Compiler\ClaimCheckerCompilerPass;
use Jose\Bundle\Checker\DependencyInjection\Compiler\HeaderCheckerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CheckerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ClaimCheckerCompilerPass());
        $container->addCompilerPass(new HeaderCheckerCompilerPass());
    }
}
