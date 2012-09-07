<?php

namespace Armd\SocialAuthBundle\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class SocialSecurityFactory implements SecurityFactoryInterface {

    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        // create auth provider
        $authProviderId = 'security.authentication.provider.armd_social_auth.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('security.authentication.provider.armd_social_auth'))
            ->replaceArgument(0, new Reference($userProvider))
            ->replaceArgument(2, $id)
        ;

        // create auth listener
        $listenerId = 'security.authentication.listener.armd_social_auth.'.$id;
        $container->setDefinition($listenerId, new DefinitionDecorator('loginza.security.authentication.listener'));

        return array($authProviderId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'social_auth';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
        $builder->children()
            ->arrayNode('auth_providers')->isRequired()->end
        ->end();
    }
}