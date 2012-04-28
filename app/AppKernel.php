<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),

            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),        
            new SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle(),
#            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),                                
            new FOS\UserBundle\FOSUserBundle(),

            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),            
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\CacheBundle\SonataCacheBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),            
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\jQueryBundle\SonatajQueryBundle(),
#            new Sonata\MediaBundle\SonataMediaBundle(),
            
            new Armd\UserBundle\ArmdUserBundle(),
            new Armd\Bundle\CmsBundle\ArmdCmsBundle(),
            new Armd\Bundle\AdminBundle\ArmdAdminBundle(),
            new Armd\MenuBundle\ArmdMenuBundle(),
#            new Armd\Bundle\TextBundle\ArmdTextBundle(),
            new Armd\NewsBundle\ArmdNewsBundle(),
#            new Armd\Bundle\MediaBundle\ArmdMediaBundle(),
#            new Armd\Bundle\AuditBundle\ArmdAuditBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles = array_merge($bundles, array(
                new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
                new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
                new Sensio\Bundle\DistributionBundle\SensioDistributionBundle(),
                new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle(),            
            ));
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}