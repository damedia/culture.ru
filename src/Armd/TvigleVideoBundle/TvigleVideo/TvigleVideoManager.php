<?php
namespace Armd\TvigleVideoBundle\TvigleVideo;

use Armd\TvigleVideoBundle\Entity\TvigleVideo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\Container;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityManager;
use Armd\TvigleVideoBundle\Pool\ConfigurationPool;

class TvigleVideoManager
{
    private $tvigleConfig;
    private $em;
    private $container;

    public function __construct(ConfigurationPool $tvigleConfig, EntityManager $em)
    {
        $this->tvigleConfig = $tvigleConfig;
        $this->em = $em;
    }

    public function setContainer(Container $container) {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
    }

    public function updateVideoDataFromTvigle(TvigleVideo $video)
    {
        if(!$video->getTvigleId()) {
            throw new \InvalidArgumentException('Tvigle id must be filled');
        }
        $soap = new \SoapClient
        (
            $this->tvigleConfig->getOption('api_service_url'),
            array
            (
                'login' => $this->tvigleConfig->getOption('api_login'),
                'password' => $this->tvigleConfig->getOption('api_password')
            )
        );

        $res = $soap->videoItem($video->getTvigleId());

        $video->setTitle($res->name);
        $video->setDescription($res->anons);
        $video->setImage($res->img);
        $video->setSwf($res->swf);
        $video->setDuration($res->duration);
        $video->setCreated(new \DateTime($res->date));

        $media = $this->createMediaFromUrl($res->img);
        $video->setImageMedia($media);

    }

    public function createMediaFromUrl($imageUrl)
    {
        $urlParts = parse_url($imageUrl);
        $fileParts = pathinfo($urlParts['path']);
        $originalName = $fileParts['basename'];

        $tempFile = tempnam(sys_get_temp_dir(), 'ArmdTvigleVideoImage');
        file_put_contents($tempFile, file_get_contents($imageUrl));
        $file = new UploadedFile($tempFile, $originalName);

        $media = new Media();
        $media->setBinaryContent($file);
        $media->setContext('lecture');
        $media->setProviderName('sonata.media.provider.image');

        return $media;

    }


}