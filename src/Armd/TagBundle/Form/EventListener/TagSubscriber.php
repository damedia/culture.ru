<?php

namespace Armd\TagBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use DoctrineExtensions\Taggable\Taggable;
use FPN\TagBundle\Entity\TagManager;
use Symfony\Component\Form\FormEvents;

class TagSubscriber implements EventSubscriberInterface
{
    private $tagManager;

    public function __construct(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getForm()->getParent()->getData();
        if ($data instanceof Taggable) {
            $this->tagManager->loadTagging($data);
        }
    }
}