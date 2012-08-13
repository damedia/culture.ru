<?php
namespace Armd\TvigleVideoBundle\TvigleVideo;

use Armd\TvigleVideoBundle\Entity\TvigleVideo;
use Doctrine\ORM\EntityManager;
use Armd\TvigleVideoBundle\Pool\ConfigurationPool;

class TvigleVideoManager
{
    private $tvigleConfig;

    public function __construct(ConfigurationPool $tvigleConfig)
    {
        $this->tvigleConfig = $tvigleConfig;
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

    }
}