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

        if ($user = $this->isAdmin()) {
            foreach ($uow->getScheduledEntityUpdates() as $entity) {
                
                if ($entity instanceof ChangeHistorySavableInterface)
                {
                    $changes = $uow->getEntityChangeSet($entity);
                    $real_changes = array();
                    foreach ($changes as $f=>$change)
                    {
                        if ($change[0] != $change[1]) 
                        {
                            $change[0] = self::getValueToSerialize($change[0]);
                            $change[1] = self::getValueToSerialize($change[1]);
                            $real_changes[$f]= $change;
                        }
                    }
                    if (count($real_changes))            
                    {
                        $changeHistory = new ChangeHistory();
                        $changeHistory->setEntityClass(get_class($entity));
                        $changeHistory->setEntityId($entity->getId());
                        $changeHistory->setChanges($real_changes);
                        $changeHistory->setUpdatedAt(new \DateTime());
                        if ($user = $this->getAuthUser())
                            $changeHistory->setUpdatedBy($user);
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
        
    }
    
    protected function getAuthUser()
    {
        if ($securityToken = $this->container->get('security.context')->getToken())
        {
            $user = $securityToken->getUser();
            if (is_object($user))
                return $user;
        }
        
        return null;
    }
    
    protected function isAdmin()
    {
        return (
            $this->container->get('security.context')->isGranted('ROLE_CORRECTOR') ||
            $this->container->get('security.context')->isGranted('ROLE_MODERATOR') ||
            $this->container->get('security.context')->isGranted('ROLE_EXPERT') ||
            $this->container->get('security.context')->isGranted('ROLE_ADMIN') ||
            $this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN') 
        );
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

        if ($em->getClassMetadata(get_class($entity))->hasField('corrected'))
        {
            if (!$this->container->get('security.context')->isGranted('ROLE_CORRECTOR'))
            {
                $entity->setCorrected(false);
            }
        }
            
    }           

    public static function getValueToSerialize($value)
    {

        if (is_object($value) && !($value instanceof Serializable) && !method_exists($value, '__sleep'))
        {
            if (method_exists($value, '__toString'))
                $value = $value->__toString();
            else 
                $value = 'value cannot be show';
        }
        elseif (is_resource($value))    
            $value = 'value cannot be show'; 
 
        return $value;       
    }        
}