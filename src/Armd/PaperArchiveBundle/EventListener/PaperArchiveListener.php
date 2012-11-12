<?php
namespace Armd\PaperArchiveBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Application\Sonata\MediaBundle\Entity\Media;
use Armd\PaperArchiveBundle\Entity\PaperArchive;

class PaperArchiveListener
{
    protected $container;

    protected $addNewPreview;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof PaperArchive) {
            $isFileChanged = $args->hasChangedField('file');
            $isImageChanged = $args->hasChangedField('image');
            $entity->setPreviewFlag(!$isImageChanged && $isFileChanged);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        if ($entity instanceof PaperArchive && $entity->getPreviewFlag()) {
            $manager = $this->container->get('armd_paper_archive.manager.paper_archive');
            $manager->createPreview($entity);
            $em->persist($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof PaperArchive) {
            if (!$entity->getImage()) {
                $em = $args->getEntityManager();
                $manager = $this->container->get('armd_paper_archive.manager.paper_archive');
                $manager->createPreview($entity);
                $em->persist($entity);
            }
        }
    }
}