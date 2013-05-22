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

    public function preSetData(DataEvent $event)
    {
        $form = $event->getForm();
        
        if (null === $event->getData()) {
            return;
        }
        
        $doctrine = $this->container->get('doctrine');       
        $data = new ArrayCollection($doctrine->getRepository('ArmdMuseumBundle:Museum')->findby(array('showOnMain' => true, 'published' => true)));
        
        $form->add($this->factory->createNamed('virtualTours', 'entity', $data, 
            array(
                'class' => 'ArmdMuseumBundle:Museum',
                'multiple' => 'true',
                'required' => false,
                'property' => 'title',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->select('partial t.{id, title}')
                        ->andWhere('t.published = TRUE');
                },
                'attr' => array('class' => 'select2 span5'),
                'mapped' => false,
        )));   
                
        $data = new ArrayCollection($doctrine->getRepository('ArmdLectureBundle:Lecture')->findby(array('showOnMain' => true, 'published' => true)));
        
        $form->add($this->factory->createNamed('lectures', 'entity', $data, 
            array(
                'class' => 'ArmdLectureBundle:Lecture',
                'multiple' => 'true',
                'required' => false,
                'property' => 'title',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->select('partial t.{id, title}')
                        ->andWhere('t.published = TRUE');
                },
                'attr' => array('class' => 'select2 span5'),
                'mapped' => false,
        )));
        
        
        $data = new ArrayCollection($doctrine->getRepository('ArmdNewsBundle:News')->findby(array('showOnMain' => true, 'published' => true)));
        
        $form->add($this->factory->createNamed('news', 'entity', $data, 
            array(
                'class' => 'ArmdNewsBundle:News',
                'multiple' => 'true',
                'required' => false,
                'property' => 'title',
                'query_builder' => function (EntityRepository $er) {
                    $newsDateFrom = new \DateTime();
                    $newsDateFrom->modify('-1 month');
                    
                    return $er->createQueryBuilder('t')
                        ->select('partial t.{id, title}')
                        ->andWhere('t.published = TRUE')
                        ->andWhere('t.newsDate >= :newsDate')->setParameter('newsDate', $newsDateFrom);
                },
                'attr' => array('class' => 'select2 span5'),
                'mapped' => false,
        )));
               
                
        $data = new ArrayCollection($doctrine->getRepository('ArmdAtlasBundle:Object')->findby(array('showOnMain' => true, 'published' => true)));
        
        $form->add($this->factory->createNamed('objects', 'entity', $data, 
            array(
                'class' => 'ArmdAtlasBundle:Object',
                'multiple' => 'true',
                'required' => false,
                'property' => 'title',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->select('partial t.{id, title}')
                        ->where('t.published = TRUE')
                        ->andWhere('t.showAtRussianImage = TRUE');
                },
                'attr' => array('class' => 'select2 span5'),
                'mapped' => false,
        )));
    }
    
    public function postBind(DataEvent $event)
    {                
        $entities = array(
            'virtualTours' => 'Armd\MuseumBundle\Entity\Museum',
            'lectures' => 'Armd\LectureBundle\Entity\Lecture',
            'news' => 'Armd\NewsBundle\Entity\News',
            'objects' => 'Armd\AtlasBundle\Entity\Object',
        );
        $form = $event->getForm();
        $em = $this->container->get('doctrine')->getEntityManager();
        $em->getConnection()->beginTransaction();
        
        try {                      
            foreach ($entities as $name => $class) {
                if ($form->has($name)) {
                    $em->createQuery('UPDATE ' . $class . ' t set t.showOnMain = FALSE')->execute();
                    
                    foreach ($form->get($name)->getData() as $e) {
                        $em->createQuery('UPDATE ' . $class . ' t set t.showOnMain = TRUE WHERE t.id = :id')
                            ->setParameter('id', $e->getId())->execute();                                               
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