<?php

namespace Armd\SocialAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Armd\SocialAuthBundle\DependencyInjection\Security\Factory\VkontakteSecurityFactory;
use Armd\SocialAuthBundle\DependencyInjection\Security\Factory\FacebookSecurityFactory;
use Armd\SocialAuthBundle\DependencyInjection\Security\Factory\TwitterSecurityFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ArmdSocialAuthBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new VkontakteSecurityFactory());
        $extension->addSecurityListenerFactory(new FacebookSecurityFactory());
        $extension->addSecurityListenerFactory(new TwitterSecurityFactory());
    }
}
