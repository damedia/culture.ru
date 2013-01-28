<?php

namespace Armd\TagBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Armd\TagBundle\DependencyInjection\Compiler\TagManagerPass;

class ArmdTagBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TagManagerPass());
    }
}
