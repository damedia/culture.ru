<?php

namespace Armd\SocialAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Armd\SocialAuthBundle\DependencyInjection\Security\Factory\SocialSecurityFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ArmdSocialAuthBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new SocialSecurityFactory());
    }
}
