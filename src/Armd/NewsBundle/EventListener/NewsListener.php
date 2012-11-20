<?php
namespace Armd\NewsBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Armd\NewsBundle\Entity\News;

class NewsListener
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $newsChanged = array();
        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            if ($entity instanceof News) {
                $newsChanged[] = $entity;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            if ($entity instanceof News) {
                $newsChanged[] = $entity;
            }
        }

        $newsManager = $this->container->get('armd_news.manager.news');

        foreach($newsChanged as $news) {
            $newsManager->updateImageDescription($news);
            $image = $news->getImage();
            $uow->computeChangeSet($em->getClassMetadata(get_class($image)), $image);
        }
    }

}