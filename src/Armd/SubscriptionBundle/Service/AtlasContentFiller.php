<?php

namespace Armd\SubscriptionBundle\Service;

use Armd\SubscriptionBundle\Entity\Issue;

/**
 * Выборка объектов на карте на сайте для формирования рассылки.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class AtlasContentFiller implements ContentFillerInterface
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

        $lastObjects = $this->entityManager->getRepository('ArmdAtlasBundle:Object')->createQueryBuilder('o')
            ->andWhere('o.createdAt BETWEEN :dateStart AND :dateEnd')
            ->andWhere('o.published = true')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->getQuery()
            ->getResult();

        return array(
            'latestObjects' => $lastObjects,
        );
    }
}
