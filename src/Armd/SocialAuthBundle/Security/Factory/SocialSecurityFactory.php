<?php

namespace Armd\SocialAuthBundle\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class SocialSecurityFactory implements SecurityFactoryInterface
{

    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        //--- create auth providers
        $container->setParameter('armd_social_auth.firewall_parameters', $config);

        // vkontakte
        $authProviderId = 'security.authentication.provider.armd_social_auth_vkontakte.' . $id;
        $container
            ->setDefinition($authProviderId, new DefinitionDecorator('security.authentication.provider.armd_social_auth_vkontakte'));

        // facebook
        $authProviderId = 'security.authentication.provider.armd_social_auth_facebook.' . $id;
        $container
            ->setDefinition($authProviderId, new DefinitionDecorator('security.authentication.provider.armd_social_auth_facebook'));


        //--- /create auth providers

        // create auth listener
        $listenerId = 'security.authentication.listener.armd_social_auth.' . $id;
        $container->setDefinition($listenerId, new DefinitionDecorator('security.authentication.listener.armd_social_auth'));

        return array($authProviderId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'armd_social_auth';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
        $builder->children()
            ->arrayNode('auth_provider_parameters')
                ->prototype('array')
                    ->prototype('array')
                        ->prototype('scalar')
                        ->end()
                ->end()
            ->end();
    }
}