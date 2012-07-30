<?php

namespace Armd\TvigleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('armd_tvigle');

        $rootNode
            ->children()
                ->scalarNode('video_directory')->defaultValue('%kernel.root_dir%/../web/uploads/video')->end()
                ->scalarNode('video_url')->defaultValue('/uploads/video/')->end()
                ->scalarNode('video_url_callback')->defaultValue('')->end()
            ->end();

        return $treeBuilder;
    }
}
