<?php

namespace Armd\LectureBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Armd\LectureBundle\Entity\Lecture;
use Symfony\Component\DependencyInjection\Container;

class LectureListener
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Lecture) {
            $tagManager = $this->container->get('fpn_tag.tag_manager');
            $tag = $tagManager->loadOrCreateTag('l' . $entity->getId());
            $tagManager->addTag($tag, $entity);
            $tagManager->saveTagging($entity);                      
        }
    }
}