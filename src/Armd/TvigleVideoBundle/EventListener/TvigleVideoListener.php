<?php

namespace Armd\TvigleVideoBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Armd\TvigleVideoBundle\TvigleVideo\TvigleVideoManager;
use Armd\TvigleVideoBundle\Entity\TvigleVideo;

class TvigleVideoListener
{
    private $tvigleManager;

    public function __construct(TvigleVideoManager $tvigleManager)
    {
        $this->tvigleManager = $tvigleManager;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof TvigleVideo) {
            $this->tvigleManager->updateVideoDataFromTvigle($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof TvigleVideo) {
            if ($args->hasChangedField('tvigleId')) {
                $this->tvigleManager->updateVideoDataFromTvigle($entity);
            }
        }
    }

}
