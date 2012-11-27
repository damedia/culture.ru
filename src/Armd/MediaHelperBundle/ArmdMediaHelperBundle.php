<?php

namespace Armd\MediaHelperBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Armd\MediaHelperBundle\DependencyInjection\Compiler\MediaProviderPass;
use Armd\MediaHelperBundle\DependencyInjection\Compiler\GaufretteReplacementPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ArmdMediaHelperBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GaufretteReplacementPass());
        $container->addCompilerPass(new MediaProviderPass());
    }
}
