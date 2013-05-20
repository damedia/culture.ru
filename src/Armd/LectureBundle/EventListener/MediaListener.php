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
            $ids = $em->createQueryBuilder()
                ->select('l.id')
                ->from('ArmdLectureBundle:Lecture', 'l')
                ->where('l.verticalBanner = :media')
                ->setParameter('media', $entity)
                ->getQuery()->getScalarResult();

            foreach($ids as $id){
                $lecture = $em->getRepository('ArmdLectureBundle:Lecture')->find($id);
                $lecture->setVerticalBanner(null);
            }


            $ids = $em->createQueryBuilder()
                ->select('l.id')
                ->from('ArmdLectureBundle:Lecture', 'l')
                ->where('l.horizontalBanner = :media')
                ->setParameter('media', $entity)
                ->getQuery()->getScalarResult();

            foreach($ids as $id){
                $lecture = $em->getRepository('ArmdLectureBundle:Lecture')->find($id);
                $lecture->setHorizontalBanner(null);
            }

        }
    }

}