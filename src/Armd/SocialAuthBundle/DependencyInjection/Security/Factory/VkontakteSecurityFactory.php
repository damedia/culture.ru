<?php

namespace Armd\SocialAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class VkontakteSecurityFactory implements SecurityFactoryInterface
{

    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $container->setParameter('armd_social_auth.firewall_parameters', $config);

        // auth provider
        $authProviderId = 'security.authentication.provider.armd_social_auth_vkontakte.' . $id;
        $container
            ->setDefinition($authProviderId, new DefinitionDecorator('security.authentication.provider.armd_social_auth_vkontakte'));

        // auth listener
        $listenerId = 'security.authentication.listener.armd_social_auth_vkontakte.' . $id;
        $container->setDefinition($listenerId, new DefinitionDecorator('security.authentication.listener.armd_social_auth_vkontakte'));

        return array($authProviderId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'armd_social_auth';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
        $builder->children()
            ->arrayNode('auth_provider_parameters')
                ->useAttributeAsKey('id')
                ->prototype('array')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->useAttributeAsKey('id')
                        ->prototype('scalar')
                        ->end()
                ->end()
            ->end();


    }
}