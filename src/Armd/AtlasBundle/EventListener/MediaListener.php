<?php

namespace Armd\AtlasBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Application\Sonata\MediaBundle\Entity\Media;

class MediaListener
{

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Media) {
            
            // unlink referenced atlas objects (image3d)
            $objects = $em->getRepository('ArmdAtlasBundle:Object')->findByImage3d($entity);
            if($objects) {
                foreach($objects as $object) {
                    $object->setImage3d(null);
                }
            }

            $objects = $em->getRepository('ArmdAtlasBundle:Object')->findBySideBannerImage($entity);
            if($objects) {
                foreach($objects as $object) {
                    $object->setSideBannerImage(null);
                }
            }

            // unlink referenced atlas objects (image3d)
            $objects = $em->getRepository('ArmdAtlasBundle:Object')->findByPrimaryImage($entity);
            if($objects) {
                foreach($objects as $object) {
                    $object->setPrimaryImage(null);
                }
            }

            // unlink referenced atlas categories
            $categories = $em->getRepository('ArmdAtlasBundle:Object')->findByVirtualTourImage($entity);
            if($categories) {
                foreach($categories as $category) {
                    $category->setVirtualTourImage(null);
                }
            }

            // unlink referenced atlas categories
            $categories = $em->getRepository('ArmdAtlasBundle:Category')->findByIconMedia($entity);
            if($categories) {
                foreach($categories as $category) {
                    $category->setIconMedia(null);
                }
            }


        }
    }

}