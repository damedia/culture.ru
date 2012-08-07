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
        if($entity instanceof Tvigle && strlen($entity->getTvigleId()) === 0) {

            $configPool = $this->container->get('armd_tvigle.configuration_pool');
            $videoUrlBase = $configPool->getOption('video_host') . $configPool->getOption('video_url');
            $video_url = $videoUrlBase.$entity->getFilename();

            $entityManager = $args->getEntityManager();
            $tvigleTask = new TvigleTask();

            $tvigleTask->setUrl($video_url);
            $tvigleTask->setVideoId($entity->getId());
            $entityManager->persist($tvigleTask);
            $entityManager->flush();
        }
    }


}