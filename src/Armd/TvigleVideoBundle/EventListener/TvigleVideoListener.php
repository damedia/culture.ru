<?php

namespace Armd\TvigleVideoBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Armd\TvigleVideoBundle\TvigleVideo\TvigleVideoManager;
use Armd\TvigleVideoBundle\Entity\TvigleVideo;

//TODO: This listener appears to be working but commented out in "Armd\TvigleVideoBundle\Resources\config\services.yml" file, so it doesn't work for now!

class TvigleVideoListener {
    private $container;

    public function setContainer(Container $container) {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if ($entity instanceof TvigleVideo) {
            $tvigleManager = $this->container->get('armd_tvigle_video.manager.tvigle_video');
            $tvigleManager->updateVideoDataFromTvigle($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        if ($entity instanceof TvigleVideo) {
            if ($args->hasChangedField('tvigleId')) {
                $tvigleManager = $this->container->get('armd_tvigle_video.manager.tvigle_video');
                $tvigleManager->updateVideoDataFromTvigle($entity);

                //TODO: This doesn't work, because of inability to change associations in preUpdate
                $uow->computeChangeSet($em->getClassMetadata('TvigleVideoBundle:TvigleVideo'), $entity);
            }
        }
    }
}