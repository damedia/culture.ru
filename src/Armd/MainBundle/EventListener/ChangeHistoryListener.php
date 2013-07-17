<?php

namespace Armd\MainBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Armd\MainBundle\Entity\ChangeHistory;
use Armd\MainBundle\Model\ChangeHistorySavableInterface;


class ChangeHistoryListener
{
    
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            
            if ($entity instanceof ChangeHistorySavableInterface)
            {
                $changes = $uow->getEntityChangeSet($entity);
                if (count($changes))            
                {
                    $changeHistory = new ChangeHistory();
                    $changeHistory->setEntityClass(get_class($entity));
                    $changeHistory->setEntityId($entity->getId());
                    $changeHistory->setChanges($changes);
                    $changeHistory->setUpdatedAt(new \DateTime());
                    if ($securityToken = $this->container->get('security.context')->getToken())
                        $changeHistory->setUpdatedBy($securityToken->getUser());
                    $changeHistory->setUpdatedIP($this->container->get('request')->server->get('REMOTE_ADDR'));
    
                    $em->persist($changeHistory);                
                    $uow->computeChangeSet(
                        $em->getClassMetadata(get_class($changeHistory)),
                        $changeHistory
                    );                
                   
                }                
            }
        }        
        
    }
    
    
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();        
        
        if ($em->getClassMetadata(get_class($entity))->hasField('corrected'))
        {
            if (!$this->container->get('security.context')->isGranted('ROLE_CORRECTOR'))
            {
                $entity->setCorrected(false);            
                $uow ->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)), $entity);             
            }    
        }
            
    }  

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();  
        
        if ($em->getClassMetadata(get_class($entity))->hasField('corrected'))
        {
            if (!$this->container->get('security.context')->isGranted('ROLE_CORRECTOR'))
            {
                $entity->setCorrected(false);            
                $uow ->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)), $entity);
            }
        }
            
    }           

       
}