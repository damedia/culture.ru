<?php
namespace Armd\TagBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TagManagerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if($container->hasDefinition('fpn_tag.tag_manager')) {
            $container->getDefinition('fpn_tag.tag_manager')
                ->setClass('Armd\TagBundle\Entity\TagManager');
        }
    }

}
