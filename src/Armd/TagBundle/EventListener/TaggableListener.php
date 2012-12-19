<?php

namespace Armd\TagBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\Container;
use DoctrineExtensions\Taggable\Taggable;
use FPN\TagBundle\Entity\TagManager;

class TaggableListener
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Taggable) {
            $this->saveTagging($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Taggable) {
            $this->saveTagging($entity);
        }
    }

    protected function saveTagging($entity)
    {
        /** @var TagManager $tagManager  */
        $tagManager = $this->container->get('fpn_tag.tag_manager');
        $tagManager->saveTagging($entity);
    }
}