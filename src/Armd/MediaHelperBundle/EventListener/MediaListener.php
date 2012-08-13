<?php

namespace Armd\MediaHelperBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Application\Sonata\MediaBundle\Entity\Media;
use Sonata\MediaBundle\Provider\ImageProvider;

use Sonata\MediaBundle\Provider\Pool;


class MediaListener
{
    private $providerPool;

    public function __construct(Pool $providerPool)
    {
        $this->providerPool = $providerPool;
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
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->removeMedia($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->removeMedia($args);
    }

    public function removeMedia(LifecycleEventArgs $args)
    {
        $media = $args->getEntity();
        $em = $args->getEntityManager();

        if ($media instanceof Media) {
            if($media->getRemoveMedia()) {
                $em->remove($media);
                $em->flush();
            }
        }

    }


}