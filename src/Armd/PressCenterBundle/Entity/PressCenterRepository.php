<?php

namespace Armd\PressCenterBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PressCenterRepository extends EntityRepository
{
    public function findForMainPage($date, $limit = 5)
    {
        $dt = new \DateTime();
        $dt->setTime(0, 0, 0);
        if ($date != '') {
            $tmp = explode('-', $date);
            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
        }

        $qb = $this->createQueryBuilder('p');
        return $qb->select('p')
            ->setMaxResults($limit)
            ->where('p.showOnMain = TRUE')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        'p.showOnMainTo IS NULL',
                        'p.showOnMainFrom IS NULL'
                    ),
                    $qb->expr()->andX(
                        'p.showOnMainFrom <= :dt',
                        'p.showOnMainTo IS NULL'
                    ),
                    $qb->expr()->andX(
                        'p.showOnMainFrom IS NULL',
                        'p.showOnMainTo >= :dt'
                    ),
                    $qb->expr()->andX('p.showOnMainFrom <= :dt', 'p.showOnMainTo >= :dt')
                )
            )
            ->setParameter('dt', $dt)
            ->orderBy('p.showOnMainTo')
            ->getQuery()
            ->getResult();
    }
}