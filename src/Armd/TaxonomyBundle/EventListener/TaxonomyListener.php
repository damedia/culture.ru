<?php

namespace Armd\TaxonomyBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;
use Armd\TaxonomyBundle\Manager\TaxonomyManager;

class TaxonomyListener
{
    private $tm;
    
    public function __construct($manager)
    {
        $this->tm = new $manager();
    }
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof TaxonomyInterface && $this->hasTags($entity)) {
            $this->tm->createTags($args->getEntityManager(), $entity);
        }
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof TaxonomyInterface && $this->hasTags($entity)) {
            $this->tm->updateTags($args->getEntityManager(), $entity);
        }
    }    

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof TaxonomyInterface && $this->hasTags($entity)) {
            $this->tm->deleteTags($args->getEntityManager(), $entity);
        }
    }
    
    function hasTags(TaxonomyInterface $entity)
    {
        $tags = "{$entity->getTags()}{$entity->getPersonalTag()}";

        return !empty($tags);
    }
}
