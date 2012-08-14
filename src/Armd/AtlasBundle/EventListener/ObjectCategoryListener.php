<?php

namespace Armd\AtlasBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Armd\AtlasBundle\Entity\Category;
use Sonata\MediaBundle\Provider\Pool;
use Application\Sonata\MediaBundle\Entity\Media;

class ObjectCategoryListener
{
    protected $providerPool;

    public function __construct(Pool $providerPool)
    {
        $this->providerPool = $providerPool;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
//        $em = $args->getEntityManager();

        if ($entity instanceof Category) {
            $iconMedia = $entity->getIconMedia();
            if($iconMedia) {
                $provider = $this->providerPool->getProvider($iconMedia->getProviderName());
                $iconUrl = $provider->getCdnPath($provider->getReferenceImage($iconMedia), $iconMedia->getCdnIsFlushable());
                $entity->setIcon($iconUrl);
            }
        }
    }

}