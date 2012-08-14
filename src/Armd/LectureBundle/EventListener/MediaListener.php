<?php

namespace Armd\LectureBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Application\Sonata\MediaBundle\Entity\Media;

class MediaListener
{

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Media) {
            $lectures = $em->getRepository('ArmdLectureBundle:Lecture')->findByLectureFile($entity);
            if($lectures) {
                foreach($lectures as $lecture) {
                    $lecture->setLectureFile(null);
                }
            }
        }
    }

}