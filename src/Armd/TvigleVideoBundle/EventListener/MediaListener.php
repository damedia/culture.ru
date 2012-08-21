<?php

namespace Armd\TvigleVideoBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Application\Sonata\MediaBundle\Entity\Media;

class MediaListener
{

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        if ($entity instanceof Media) {
            $videos = $em->getRepository('ArmdTvigleVideoBundle:TvigleVideo')->findByImageMedia($entity);
            foreach($videos as $video) {
                $video->setImageMedia(null);
            }
        }

    }

}
