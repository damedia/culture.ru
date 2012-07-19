<?php

namespace Zim32\LoginzaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zim32\LoginzaBundle\DependencyInjection\Security\Factory\LoginzaFactory;

class Zim32LoginzaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new LoginzaFactory());
    }
}
