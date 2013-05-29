<?php

namespace Armd\SubscriptionBundle\Service;

use Armd\SubscriptionBundle\Entity\Issue;

/**
 * Выборка лекций на сайте для формирования рассылки.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class LectureContentFiller implements ContentFillerInterface
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

        $lastLectures = $this->entityManager->getRepository('ArmdLectureBundle:Lecture')->createQueryBuilder('l')
            ->andWhere('l.createdAt BETWEEN :dateStart AND :dateEnd')
            ->andWhere('l.published = true')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->getQuery()
            ->getResult();

        return array(
            'latestLectures' => $lastLectures,
        );
    }
}
