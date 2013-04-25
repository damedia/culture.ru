<?php

namespace Armd\SitemapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ArmdSitemapExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('armd_sitemap.host', $config['host']);
        $container->setParameter('armd_sitemap.scheme', $config['scheme']);
        $container->setParameter('armd_sitemap.default_locale', $config['default_locale']);
        $container->setParameter('armd_sitemap.locales', $config['locales']);
        $container->setParameter('armd_sitemap.directory', $config['directory']);
        $container->setParameter('armd_sitemap.urls_per_file', $config['urls_per_file']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    }
}
