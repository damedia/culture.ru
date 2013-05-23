<?php
namespace Armd\MediaHelperBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MediaProviderPass implements CompilerPassInterface
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
        if($container->hasDefinition('sonata.media.provider.culturetv')) {
            $configPool = $container->get('armd_media_helper.configuration_pool');

            $providers = $configPool->getOption('providers');

            $container->getDefinition('sonata.media.provider.culturetv')
                ->replaceArgument(1, new Reference($providers['culturetv']['filesystem']))
                ->replaceArgument(3, new Reference($providers['culturetv']['generator']))
                ->replaceArgument(4, new Reference($providers['culturetv']['thumbnail']))
                ->addMethodCall('setResizer', array(new Reference($providers['culturetv']['resizer'])))
            ;
        }

        if($container->hasDefinition('sonata.media.provider.tvigle')) {
            $configPool = $container->get('armd_media_helper.configuration_pool');

            $providers = $configPool->getOption('providers');

            $container->getDefinition('sonata.media.provider.tvigle')
                ->replaceArgument(1, new Reference($providers['tvigle']['filesystem']))
                ->replaceArgument(3, new Reference($providers['tvigle']['generator']))
                ->replaceArgument(4, new Reference($providers['tvigle']['thumbnail']))
                ->replaceArgument(6, $providers['tvigle']['api'])
                ->addMethodCall('setResizer', array(new Reference($providers['tvigle']['resizer'])))
            ;
        }
    }

}
