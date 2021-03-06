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
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\CommentBundle\FOSCommentBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
        	
        	new Damedia\TinymceBundle\DamediaTinymceBundle(),
            // new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),

            new Genemu\Bundle\FormBundle\GenemuFormBundle(),

            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\CacheBundle\SonataCacheBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new FPN\TagBundle\FPNTagBundle(),
            new Armd\MainBundle\ArmdMainBundle(),
            new Armd\UserBundle\ArmdUserBundle(),
            new Armd\AtlasBundle\ArmdAtlasBundle(),
            new Armd\TvigleVideoBundle\ArmdTvigleVideoBundle(),
            new Armd\ListBundle\ArmdListBundle(),
            new Armd\NewsBundle\ArmdNewsBundle(),
            new Armd\ChronicleBundle\ArmdChronicleBundle(),
            new Armd\BannerBundle\ArmdBannerBundle(),
            new Armd\SearchBundle\ArmdSearchBundle(),
            new Armd\SphinxSearchBundle\ArmdSphinxSearchBundle(),
            new Armd\LectureBundle\ArmdLectureBundle(),
            new Armd\MediaHelperBundle\ArmdMediaHelperBundle(),
            new Armd\EventBundle\ArmdEventBundle(),
            new Armd\SocialAuthBundle\ArmdSocialAuthBundle(),
            new Armd\MkCommentBundle\ArmdMkCommentBundle(),
            new Armd\UtilBundle\ArmdUtilBundle(),
            new Armd\MuseumBundle\ArmdMuseumBundle(),
            new Armd\OAuthBundle\ArmdOAuthBundle(),
            new Armd\PaperArchiveBundle\ArmdPaperArchiveBundle(),
            new Armd\TwigBundle\ArmdTwigBundle(),
            new Armd\TagBundle\ArmdTagBundle(),
            new Armd\DoctrineBundle\ArmdDoctrineBundle(),
            new Armd\OnlineTranslationBundle\ArmdOnlineTranslationBundle(),
            new Armd\AdminHelperBundle\ArmdAdminHelperBundle(),
            new Armd\PollBundle\ArmdPollBundle(),
            new Armd\AddressBundle\ArmdAddressBundle(),
            new Armd\ExhibitBundle\ArmdExhibitBundle(),
            new Armd\PersonBundle\ArmdPersonBundle(),
            new Armd\TheaterBundle\ArmdTheaterBundle(),
            new Armd\PerfomanceBundle\ArmdPerfomanceBundle(),

            new Armd\SitemapBundle\ArmdSitemapBundle(),
            new Armd\ExternalSearchBundle\ArmdExternalSearchBundle(),
            new Armd\MarkupBundle\ArmdMarkupBundle(),
            new Armd\ExtendedBannerBundle\ArmdExtendedBannerBundle(),
            new Armd\TouristRouteBundle\ArmdTouristRouteBundle(),
            new Armd\SubscriptionBundle\ArmdSubscriptionBundle(),
            new Armd\DCXBundle\ArmdDCXBundle(),
            new Damedia\SpecialProjectBundle\DamediaSpecialProjectBundle(),
            new Armd\BlogBundle\BlogBundle(),
            new Armd\ActualInfoBundle\ActualInfoBundle(),
            new Damedia\SonataAdminWrapperBundle\DamediaSonataAdminWrapperBundle(),
            new Damedia\AtlasJsModuleBundle\DamediaAtlasJsModuleBundle(),
            new Damedia\CustomRouterBundle\DamediaCustomRouterBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test', 'dev_en'))) {
            $bundles = array_merge($bundles, array(
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
