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
            \gFuncs::dbgWriteLogVar($entity->getId(), false, 'LectureBundle\MediaListener preRemove '); // DBG:

            $lectures = $em->getRepository('ArmdLectureBundle:Lecture')->findByVerticalBanner($entity);
            \gFuncs::dbgWriteLogVar(count($lectures), false, 'LectureBundle\MediaListener preRemove count vertical'); // DBG:
            if($lectures) {
                foreach($lectures as $lecture) {
                    \gFuncs::dbgWriteLogDoctrine($lecture, 2, false, 'preRemove vertical'); // DBG:
                    $lecture->setVerticalBanner(null);
                }
            }

            $lectures = $em->getRepository('ArmdLectureBundle:Lecture')->findByHorizontalBanner($entity);
            \gFuncs::dbgWriteLogVar(count($lectures), false, 'LectureBundle\MediaListener preRemove count horizontal'); // DBG:
            if($lectures) {
                foreach($lectures as $lecture) {
                    $lecture->setHorizontalBanner(null);
                }
            }


        }
    }

}