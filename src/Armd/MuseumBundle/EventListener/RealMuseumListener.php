<?php

namespace Armd\MuseumBundle\EventListener;

use Armd\MuseumBundle\Entity\RealMuseum;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\LifecycleEventArgs;


class RealMuseumListener
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof RealMuseum) {
            $tagManager = $this->container->get('fpn_tag.tag_manager');
            $tag = $tagManager->loadOrCreateTag('realmuseum' . $entity->getId());
            $tag->setIsTechnical(true);
            $tagManager->addTag($tag, $entity);
            $tagManager->saveTagging($entity);
        }
    }
}