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

        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->where('p.showOnMain = TRUE')
            ->andWhere('p.showOnMainFrom <= :dt')
            ->andWhere('p.showOnMainTo >= :dt')
            ->setParameter('dt', $dt)
            ->orderBy('p.showOnMainTo')
            ->getQuery()
            ->getArrayResult();
    }
}