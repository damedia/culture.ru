<?php

namespace Armd\TouristRouteBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Application\Sonata\MediaBundle\Entity\Media;

class MediaListener
{
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em     = $args->getEntityManager();

        if ($entity instanceof Media) {
            // unlink referenced tourist route primary image
            $objects = $em->getRepository('ArmdTouristRouteBundle:Route')->findByPrimaryImage($entity);
            
            if($objects) {
                foreach($objects as $object) {
                    $object->setPrimaryImage(null);
                }
            }

            // unlink referenced tourist routes categories icon
            $categories = $em->getRepository('ArmdTouristRouteBundle:Category')->findByIcon($entity);
            
            if ($categories) {
                foreach($categories as $category) {
                    $category->setIcon(null);
                }
            }
        }
    }
}
