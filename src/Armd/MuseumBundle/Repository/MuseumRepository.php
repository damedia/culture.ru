<?php

namespace Armd\MuseumBundle\Repository;

use Armd\MuseumBundle\Entity\Museum;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class MuseumRepository extends EntityRepository
{
    /**
     * @param string $date
     * @param integer $limit
     * @return Museum|null
     */
    public function findForMain($date = '', $limit = 3)
    {
        $dt = new \DateTime();
        $dt->setTime(0, 0, 0);
        if ($date != '') {
            $tmp = explode('-', $date);
            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
        }

        $qb = $this->createQueryBuilder('a');

        return $qb->where('a.showOnMain = :show')
            ->setParameter('show', true)
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        'a.showOnMainTo IS NULL',
                        'a.showOnMainFrom IS NULL'
                    ),
                    $qb->expr()->andX(
                        'a.showOnMainFrom <= :dt',
                        'a.showOnMainTo IS NULL'
                    ),
                    $qb->expr()->andX(
                        'a.showOnMainFrom IS NULL',
                        'a.showOnMainTo >= :dt'
                    ),
                    $qb->expr()->andX('a.showOnMainFrom <= :dt', 'a.showOnMainTo >= :dt')
                )
            )
            ->setParameter('dt', $dt)
            ->setMaxResults($limit)
            ->orderBy('a.showOnMainOrd')
            ->getQuery()
            ->getResult();

    }
}