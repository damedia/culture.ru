<?php


namespace Armd\MediaHelperBundle\Twig;

use Sonata\MediaBundle\Provider\Pool;
use Application\Sonata\MediaBundle\Entity\Media;

class MediaHelperExtension extends \Twig_Extension
{
    private $providerPool;

    public function __construct(Pool $providerPool)
    {
        $this->providerPool = $providerPool;
    }

    public function getFunctions()
    {
        return array(
            'armd_media_original_url' => new \Twig_Function_Method($this, 'originalUrl'),
            'armd_media_original_img' => new \Twig_Function_Method($this, 'originalImage')
        );
    }

    public function originalUrl(Media $media = null)
    {
        if(is_null($media)) {
            $url = '';
        } else {
            $provider = $this->providerPool->getProvider($media->getProviderName());
            $url = $provider->getCdnPath($provider->getReferenceImage($media), $media->getCdnIsFlushable());
        }

        return $url;
    }

    public function originalImage(Media $media = null)
    {
        $url = $this->originalUrl($media);
        if(!empty($url)) {
            $html = '<img src="' . $url . '" />';
        } else {
            $html = '';
        }
        return $html;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'armd_media_helper_extension';
    }
}