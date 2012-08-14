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

            // unlink referenced atlas objects
            $objects = $em->getRepository('ArmdAtlasBundle:Object')->findByImage3d($entity);
            if($objects) {
                foreach($objects as $object) {
                    $object->setImage3d(null);
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