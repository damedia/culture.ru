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
            ->andWhere('p.showOnMainFrom <= :dt1')
            ->andWhere($qb->expr()->orX('p.showOnMainTo > :dt1', 'p.showOnMainTo IS NULL'))
            ->setParameter('dt1', $dt)
            ->orderBy('p.showOnMainTo')
            ->getQuery()
            ->getResult();
    }
}