<?php

namespace Armd\PersonBundle\EventListener;

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
            $objects = $em->getRepository('ArmdPersonBundle:Person')->findByImage($entity);
            if($objects) {
                foreach($objects as $object) {
                    $object->setImage(null);
                }
            }
        }
    }

}