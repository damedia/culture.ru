<?php

namespace Armd\TouristRouteBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Armd\TouristRouteBundle\Entity\Category;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Query\Expr;

class CategoryListener
{

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em     = $args->getEntityManager();

        if ($entity instanceof Category) {
            $objects = $em->getRepository('ArmdTouristRouteBundle:Route')
                ->createQueryBuilder('o')
                    ->leftJoin('o.categories', 'c')
                    ->where('c.id = :category')
                    ->setParameter('category', $entity)
                    ->getQuery()
                        ->getResult();

            if ($objects) {
                foreach ($objects as $object) {
                    $object->removeCategory($entity);
                }
            }
        }
    }
}
