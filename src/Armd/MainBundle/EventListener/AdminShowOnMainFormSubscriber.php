<?php

namespace Armd\MainBundle\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_BIND => 'postBind'
        );
    }

    protected function getFields()
    {
        return array(
            'virtualTours' => 'Armd\MuseumBundle\Entity\Museum',
            'lectures' => 'Armd\LectureBundle\Entity\Lecture',
            'news' => 'Armd\NewsBundle\Entity\News',
            'objects' => 'Armd\AtlasBundle\Entity\Object',
        );
    }
    public function preSetData(DataEvent $event)
    {              
        $form = $event->getForm();
        
        if (null === $event->getData()) {
            return;
        }
         
        $em = $this->container->get('doctrine')->getEntityManager();
        
        foreach ($this->getFields() as $name => $class) {
            $choises = $data = array();
            $choicesResult = $em
                ->createQuery('SELECT t.id, t.title, t.showOnMain FROM ' . $class . ' t WHERE t.published = TRUE ORDER BY t.title')
                ->getScalarResult();      

            foreach ($choicesResult as $row) {
                $choises[$row['id']] = $row['title'];

                if ($row['showOnMain']) {
                    $data[] = $row['id'];
                }
            }

            $form->add($this->factory->createNamed($name, 'choice', $data, 
                array(
                    'choices'   => $choises,
                    'multiple' => 'true',
                    'required'  => false,
                    'attr' => array('class' => 'select2 span5'),
                    'virtual' => true,
            )));
        }                   
    }
    
    public function postBind(DataEvent $event)
    {                       
        $form = $event->getForm();
        $em = $this->container->get('doctrine')->getEntityManager();
        $em->getConnection()->beginTransaction();
        
        try {                      
            foreach ($this->getFields() as $name => $class) {
                if ($form->has($name)) {
                    $em->createQuery('UPDATE ' . $class . ' t set t.showOnMain = FALSE')->execute();
                    
                    foreach ($form->get($name)->getData() as $id) {                       
                        $em->createQuery('UPDATE ' . $class . ' t set t.showOnMain = TRUE WHERE t.id = :id')
                            ->setParameter('id', $id)->execute();                                               
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