<?php

namespace Armd\TvigleBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Armd\TvigleBundle\Entity\Tvigle;
use Armd\TvigleBundle\Entity\TvigleTask;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


class DBEventListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Tvigle) {
            $entity->setCreated(new \DateTime());
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Tvigle) {

            $videoUrlBase = $this->container->get('armd_tvigle.configuration_pool')->getOption('video_url');
            $key = $this->container->get('request')->request->keys();
            $key = $key[0];
            $requestData = $this->container->get('request')->get($key);
            $filename = $requestData['filename'];
            $request = $this->container->get('request');
            $video_url = $request->getScheme().'://'.$request->getHttpHost(). $videoUrlBase.$filename;

            $entityManager = $args->getEntityManager();
            $tvigleTask = new TvigleTask();

            $tvigleTask->setUrl($video_url);
            $tvigleTask->setVideoId($entity->getId());
            $entityManager->persist($tvigleTask);
            $entityManager->flush();
        }
    }

}