<?php

namespace Armd\AtlasBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Armd\AtlasBundle\Entity\Category;
use Application\Sonata\MediaBundle\Entity\Media;

class CategoryListener
{

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Category) {

            $objects = $em->getRepository('ArmdAtlasBundle:Object')
                ->createQueryBuilder('o')
                ->where('o.primaryCategory = :category')
                ->getQuery()
                ->setParameters(array(
                    'category' => $entity
                ))
                ->getResult();

            if ($objects) {
                foreach ($objects as $object) {
                    $object->setPrimaryCategory(null);
                }
            }

//            $objects = $em->getRepository('ArmdAtlasBundle:Object')
//                ->createQueryBuilder('o')
//                ->where('o.secondaryCategories = :category')
//                ->getQuery()
//                ->setParameters(array(
//                    'category' => $entity
//                ))

//            // unlink referenced atlas objects
//            $objects = $em->getRepository('ArmdAtlasBundle:Object')
//                ->createQueryBuilder('o')
//                ->where('o.secondaryCategories = :category')
//                ->getQuery()
//                ->setParameters(array(
//                    'category' => $entity
//                ))
//                ->getResult();
////                ->innerJoin('o.secondaryCategories', 'sc')
////                ->where('sc)
//
//            if ($objects) {
//                foreach ($objects as $object) {
//                    $object->removeSecondaryCategory($entity);
//                }
//            }

//            // unlink referenced atlas categors
//            $categories = $em->getRepository('ArmdAtlasBundle:Category')->findByIconMedia($entity);
//            if ($categories) {
//                foreach ($categories as $category) {
//                    $category->setIconMedia(null);
//                }
//            }
        }
    }

}