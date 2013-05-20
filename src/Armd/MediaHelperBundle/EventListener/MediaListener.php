<?php

namespace Armd\MediaHelperBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Application\Sonata\MediaBundle\Entity\Media;
use Sonata\MediaBundle\Provider\ImageProvider;

use Sonata\MediaBundle\Provider\Pool;


class MediaListener
{
    private $providerPool;
    private $pendingForRemovalMedias;

    public function __construct(Pool $providerPool)
    {
        $this->providerPool = $providerPool;
        $this->pendingForRemovalMedias = array();
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $media = $args->getEntity();

        if ($media instanceof Media) {
            $formFile = $media->getFormFile();
            if(!empty($formFile)) {
                $provider = $this->providerPool->getProvider($media->getProviderName());
                $provider->preRemove($media->getMediaBeforeUpdate());
                $media->resetMediaBeforeUpdate();
            }
            if($media->getRemoveMedia()) {
                $this->pendingForRemovalMedias[] = $media;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if(count($this->pendingForRemovalMedias)) {
            $em = $args->getEntityManager();
            foreach($this->pendingForRemovalMedias as $media)
            {
                $em->remove($media);
            }
            $this->pendingForRemovalMedias = array();
            $em->flush();
        }
    }

}