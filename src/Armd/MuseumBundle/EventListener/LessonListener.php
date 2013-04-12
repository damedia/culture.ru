<?php

namespace Armd\MuseumBundle\EventListener;

use Armd\MuseumBundle\Entity\Lesson;
use Symfony\Component\DependencyInjection\Container;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Event\LifecycleEventArgs;


class LessonListener
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Lesson) {
            $tagManager = $this->container->get('fpn_tag.tag_manager');
            $tag = $tagManager->loadOrCreateTag('lesson' . $entity->getId());
            $tag->setIsTechnical(true);
            $tagManager->addTag($tag, $entity);
            $tagManager->saveTagging($entity);                      
        }
    }
}