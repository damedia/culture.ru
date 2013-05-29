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
            $fields = \Armd\MainBundle\Admin\ShowOnMain::getFields();
            
            foreach ($form->all() as $name => $field) {
                if (isset($fields[$name])) {
                    $class = $fields[$name];
                    $category = false;
                } elseif (preg_match("~^news-(\d+)$~", $name, $matches)) {
                    $class = 'Armd\NewsBundle\Entity\News';
                    $category = $matches[1];
                } else {
                    continue;
                }
                
                 $qb = $em->getRepository($class)->createQueryBuilder('t')->update();
                    
                if ($category) {                      
                    $qb->where('t.category = :category')->setParameter('category', $category);
                }

                $qb1 = clone $qb;
                $qb1->set('t.showOnMain', 'FALSE')->getQuery()->execute();

                foreach (preg_split('~,~', $field->getData()) as $id) {
                    if ($id) {
                        $qb2 = clone $qb;
                        $qb2->set('t.showOnMain', 'TRUE')
                            ->andWhere('t.id = :id')->setParameter('id', $id)
                            ->getQuery()->execute();                                                                         
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