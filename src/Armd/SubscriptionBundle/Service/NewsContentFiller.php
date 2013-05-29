<?php

namespace Armd\SubscriptionBundle\Service;

use Armd\SubscriptionBundle\Entity\Issue;

/**
 * Выборка новостей на сайте для формирования рассылки.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class NewsContentFiller implements ContentFillerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Armd\SubscriptionBundle\Entity\Issue $issue
     *
     * @return string
     */
    public function getContentFor(Issue $issue)
    {
        $date = clone $issue->getCreatedAt();
        $date = $date->modify('-1 day');

        $dateStart = clone $date;
        $dateStart->setTime(0, 0, 0);

        $dateEnd = clone $date;
        $dateEnd->setTime(23, 59, 59);

        $lastNews = $this->entityManager->getRepository('ArmdNewsBundle:News')->createQueryBuilder('n')
            ->andWhere('n.newsDate BETWEEN :dateStart AND :dateEnd')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->getQuery()
            ->getResult();

        return array(
            'latestNews' => $lastNews,
        );
    }
}
