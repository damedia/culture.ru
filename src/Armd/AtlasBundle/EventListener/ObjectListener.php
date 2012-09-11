<?php

namespace Armd\AtlasBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Armd\AtlasBundle\Entity\Object;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;

class ObjectListener
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Object) {
            // sync categories
            $entity->syncPrimaryAndSecondaryCategories();
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        if ($entity instanceof Object) {
            // sync categories
            $entity->syncPrimaryAndSecondaryCategories();
            $uow->computeChangeSet(
                $em->getClassMetadata('ArmdAtlasBundle:Object'),
                $entity
            );
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $aclProvider = $this->container->get('security.acl.provider');
        $entity = $args->getEntity();

        if($entity instanceof Object) {
            // remove acl
            $objectIdentity = ObjectIdentity::fromDomainObject($entity);
            try {
                $aclProvider->deleteAcl($objectIdentity);
            } catch (Exception $ex) {}
        }

    }

}