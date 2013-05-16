<?php

namespace Armd\ExtendedBannerBundle\Twig;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;
use Sonata\MediaBundle\Provider\Pool;

class BannerExtension extends \Twig_Extension
{
    private $em;
    private $router;

    public function __construct(EntityManager $em, Router $router, Pool $media)
    {
        $this->em = $em;
        $this->router = $router;
        $this->media = $media;
    }


    public function getFunctions()
    {
        return array(
            'armd_banner' => new \Twig_Function_Method($this, 'bannerFunction'),
            'armd_has_banner' => new  \Twig_Function_Method($this, 'hasBannerFunction')
        );
    }

    public function bannerFunction($bannerTypeCode)
    {
        $html = '';
        $banner = $this->em->getRepository('ArmdExtendedBannerBundle:BaseBanner')->getBanner($bannerTypeCode);
        if(!empty($banner)) {
            $banner->addView();
            $this->em->flush();

            if($banner->getOpenInNewWindow()) {
                $target = 'target="_blank"';
            } else {
                $target = '';
            }

            $html = "<a href=\"" . $this->router->generate('armd_banner_click', array('bannerId' => $banner->getId())) . "\" $target><img src=\"" . $this->media->path($banner->getImage(), 'reference') . "\" /></a>";
        }

        return $html;
    }

    public function hasBannerFunction($bannerTypeCode)
    {
        return $this->em->getRepository('ArmdExtendedBannerBundle:BaseBanner')->hasActiveBanner($bannerTypeCode);
    }

    public function getName()
    {
        return 'armd_banner_extension';
    }

}