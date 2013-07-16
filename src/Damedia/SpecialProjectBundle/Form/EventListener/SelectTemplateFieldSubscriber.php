<?php
namespace Damedia\SpecialProjectBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SelectTemplateFieldSubscriber implements EventSubscriberInterface {
    public function __construct(FormFactoryInterface $factory) {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents() {
        return array(FormEvents::POST_SET_DATA => 'postSetData');
    }

    public function postSetData(FormEvent $event) {
        //$form = $event->getForm();
        //$data = $event->getData();

        //$form->add($this->factory->createNamed('someShit', 'text', array(). array()));
    }
}
?>