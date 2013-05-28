<?php

namespace Armd\MainBundle\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Armd\MainBundle\EventListener\EntityShowOnMainSubscriber;

class AdminShowOnMainFormSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $container;

    public function __construct(FormFactoryInterface $factory, ContainerInterface $container)
    {
        $this->factory = $factory;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {        
        return array(
            FormEvents::POST_BIND => 'postBind'
        );
    }
    
    public function postBind(DataEvent $event)
    {                       
        $form = $event->getForm();
        $em = $this->container->get('doctrine')->getEntityManager();
        $em->getConnection()->beginTransaction();
        
        try {                      
            foreach (\Armd\MainBundle\Admin\ShowOnMain::getFields() as $name => $class) {
                if ($form->has($name)) {                   
                    $em->createQuery('UPDATE ' . $class . ' t set t.showOnMain = FALSE')->execute();
                    
                    foreach (preg_split('~,~', $form->get($name)->getData()) as $id) {
                        if ($id) {
                            $em->createQuery('UPDATE ' . $class . ' t set t.showOnMain = TRUE WHERE t.id = :id')
                                ->setParameter('id', $id)->execute();                                               
                        }
                    }
                }
            }
            
            $em->getConnection()->commit();    
            $em->getEventManager()->addEventSubscriber(new EntityShowOnMainSubscriber());
        } catch (Exception $e) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }      
    }
}