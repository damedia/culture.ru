<?php

namespace Armd\DCXBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ArmdDCXExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        if (!empty($config['auth']) && !empty($config['settings'])) {
            $this->curlConstantLoad($config['auth'], $container);
            $container->setParameter('dcx.auth.userpwd', $config['auth']['dcx_auth_userpwd']);
            $container->setParameter('dcx.auth.cookiejar', $config['auth']['dcx_auth_cookiefile']);
            $container->setParameter('dcx.auth.cookiefile', $config['auth']['dcx_auth_cookiejar']);
            $container->setParameter('dcx.host', $config['settings']['host']);
        }

    }

    /**
     * Loads the DCX auth configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function curlConstantLoad(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('curl.constants.xml');
    }
}
