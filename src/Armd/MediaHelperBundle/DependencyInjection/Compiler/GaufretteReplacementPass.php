<?php
namespace Armd\MediaHelperBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GaufretteReplacementPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if($container->hasDefinition('sonata.media.adapter.filesystem.ftp')) {
            $container->getDefinition('sonata.media.adapter.filesystem.ftp')
                ->setClass('Armd\MediaHelperBundle\Gaufrette\Adapter\Ftp');
        }
    }

}
