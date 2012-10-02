<?php

namespace Armd\MkCommentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Armd\MkCommentBundle\DependencyInjection\Compiler\ServiceReplacementPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ArmdMkCommentBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSCommentBundle';
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ServiceReplacementPass());
    }
}
