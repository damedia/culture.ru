<?php

namespace Armd\AtlasBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Application\Sonata\MediaBundle\Entity\Media;
use Sonata\MediaBundle\Provider\ImageProvider;

class MediaListener
{
    private $imageProvider;

    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $media = $args->getEntity();

        if ($media instanceof Media) {
            $formImage = $media->getFormImageFile();
            if(!empty($formImage)) {
                $this->imageProvider->preRemove($media->getMediaImageBeforeUpdate());
                $media->resetMediaImageBeforeUpdate();
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->removeMedia($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->removeMedia($args);
    }

    public function removeMedia(LifecycleEventArgs $args)
    {
        $media = $args->getEntity();
        $em = $args->getEntityManager();

        if ($media instanceof Media) {
            if($media->getRemoveMedia()) {
                $objects = $em->getRepository('ArmdAtlasBundle:Object')->findByImage3d($media);
                if($objects) {
                    foreach($objects as $object) {
                        $object->setImage3d(null);
                    }
                }
                $em->remove($media);
                $em->flush();
            }
        }

    }


}